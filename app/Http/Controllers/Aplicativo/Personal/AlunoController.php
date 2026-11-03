<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Acesso;
use App\Models\Aluno;
use App\Support\LinkMagico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Alunos do Denilson: cadastro com um ou mais telefones, link mágico e aparelhos liberados.
 */
class AlunoController extends Controller
{
    public function index(): JsonResponse
    {
        $alunos = Aluno::with('telefones')
            ->withCount(['treinos', 'acessos as aparelhos_count' => fn ($q) => $q->whereNotNull('ace_token_hash')->whereNull('ace_revogado_em')])
            ->orderBy('alu_nome')->get();

        return response()->json(['alunos' => $alunos->map(fn ($a) => $this->formato($a))->values()]);
    }

    public function show(string $aluHashid): JsonResponse
    {
        return response()->json($this->formato(Aluno::with('telefones')->hashid($aluHashid)->firstOrFail()));
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $this->validar($request);
        $aluno = DB::transaction(function () use ($dados) {
            $aluno = Aluno::create(['alu_nome' => $dados['alu_nome'], 'alu_obs' => $dados['alu_obs'] ?? null]);
            $this->salvarTelefones($aluno, $dados['telefones']);

            return $aluno;
        });

        return response()->json($this->formato($aluno->load('telefones')), 201);
    }

    public function update(Request $request, string $aluHashid): JsonResponse
    {
        $aluno = Aluno::hashid($aluHashid)->firstOrFail();
        $dados = $this->validar($request, true);
        DB::transaction(function () use ($aluno, $dados) {
            $aluno->update(array_intersect_key($dados, array_flip(['alu_nome', 'alu_obs', 'alu_situacao'])));
            if (isset($dados['telefones'])) {
                $this->salvarTelefones($aluno, $dados['telefones']);
            }
        });

        return response()->json($this->formato($aluno->fresh('telefones')));
    }

    public function destroy(string $aluHashid): JsonResponse
    {
        Aluno::hashid($aluHashid)->firstOrFail()->delete();

        return response()->json(['message' => 'Aluno excluído.']);
    }

    /** Gera o link mágico e o código; devolve o link do WhatsApp pronto para cada telefone do aluno. */
    public function link(string $aluHashid): JsonResponse
    {
        $aluno = Aluno::with('telefones')->hashid($aluHashid)->firstOrFail();
        ['link' => $link, 'codigo' => $codigo] = LinkMagico::gerar($aluno);
        $primeiroNome = strtok($aluno->alu_nome, ' ');
        $mensagem = "Olá {$primeiroNome}! Suas fichas de treino do Personal DEF já estão no aplicativo.\n\nToque para liberar no seu celular: {$link}\n\nJá tem o app instalado? Abra e digite o código {$codigo}.\n\n(Link válido por ".LinkMagico::VALIDADE_DIAS.' dias e para um aparelho.)';

        return response()->json([
            'link' => $link,
            'codigo' => $codigo,
            'mensagem' => $mensagem,
            'whatsapp' => $aluno->telefones->map(fn ($t) => [
                'tel_numero' => $t->tel_numero,
                'url' => 'https://wa.me/'.$t->tel_numero.'?text='.rawurlencode($mensagem),
            ])->values(),
        ]);
    }

    public function aparelhos(string $aluHashid): JsonResponse
    {
        $aluno = Aluno::hashid($aluHashid)->firstOrFail();
        $acessos = $aluno->acessos()->whereNotNull('ace_token_hash')->whereNull('ace_revogado_em')->orderByDesc('ace_ultimo_uso')->get();

        return response()->json(['aparelhos' => $acessos->map(fn (Acesso $a) => [
            'ace_hashid' => $a->ace_hashid,
            'ace_aparelho' => $a->ace_aparelho,
            'liberado_em' => $a->ace_link_usado_em?->format('d/m/Y H:i'),
            'ultimo_uso' => $a->ace_ultimo_uso?->format('d/m/Y H:i'),
        ])->values()]);
    }

    public function revogarAparelho(string $aceHashid): JsonResponse
    {
        Acesso::hashid($aceHashid)->whereNotNull('alu_id')->firstOrFail()->forceFill(['ace_revogado_em' => now()])->save();

        return response()->json(['message' => 'Aparelho desconectado.']);
    }

    private function validar(Request $request, bool $parcial = false): array
    {
        $req = $parcial ? 'sometimes' : 'required';

        return $request->validate([
            'alu_nome' => [$req, 'string', 'max:150'],
            'alu_obs' => ['nullable', 'string', 'max:2000'],
            'alu_situacao' => ['sometimes', 'in:AT,IN'],
            'telefones' => [$req, 'array', 'min:1', 'max:5'],
            'telefones.*' => ['required', 'string', 'regex:/^[\d\s()+\-]{10,20}$/'],
        ]);
    }

    private function salvarTelefones(Aluno $aluno, array $telefones): void
    {
        $numeros = collect($telefones)->map(fn ($t) => self::normalizarTelefone($t))->filter()->unique()->values();
        $aluno->telefones()->whereNotIn('tel_numero', $numeros)->delete();
        foreach ($numeros as $i => $numero) {
            $aluno->telefones()->updateOrCreate(['tel_numero' => $numero], ['tel_principal' => $i === 0]);
        }
    }

    /** Só dígitos, com DDI 55 quando vier só DDD + número. */
    public static function normalizarTelefone(string $telefone): ?string
    {
        $digitos = preg_replace('/\D/', '', $telefone);
        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            $digitos = '55'.$digitos;
        }

        return strlen($digitos) >= 12 && strlen($digitos) <= 15 ? $digitos : null;
    }

    private function formato(Aluno $aluno): array
    {
        return [
            'alu_hashid' => $aluno->alu_hashid,
            'alu_nome' => $aluno->alu_nome,
            'alu_obs' => $aluno->alu_obs,
            'alu_situacao' => $aluno->alu_situacao,
            'telefones' => $aluno->telefones->pluck('tel_numero')->values(),
            'qtd_fichas' => $aluno->treinos_count ?? $aluno->treinos()->count(),
            'qtd_aparelhos' => $aluno->aparelhos_count ?? null,
        ];
    }
}
