<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Cadencia;
use App\Models\Exercicio;
use App\Models\Metodo;
use App\Models\Pausa;
use App\Models\Treino;
use App\Models\TreinoExercicio;
use App\Support\FichaFormato;
use App\Support\Hashid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Fichas do Denilson: genéricas (públicas ou não) e exclusivas por aluno, com os exercícios.
 * Baseado no TreinoController/TreinoExercicioController do 69fit.
 */
class FichaController extends Controller
{
    /** ?aluno={alu_hashid} lista as do aluno; sem parâmetro, as genéricas do Denilson. */
    public function index(Request $request): JsonResponse
    {
        $alunoId = $request->filled('aluno') ? Aluno::hashid($request->query('aluno'))->firstOrFail()->alu_id : null;
        $fichas = Treino::where('alu_id', $alunoId)->where('tre_situacao', '<>', 'IN')->withCount('exercicios')->orderBy('tre_ordem')->get();

        return response()->json(['fichas' => $fichas->map(fn ($t) => FichaFormato::resumo($t))->values()]);
    }

    public function show(string $treHashid): JsonResponse
    {
        return response()->json(FichaFormato::completa(Treino::hashid($treHashid)->firstOrFail()));
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'tre_titulo' => ['required', 'string', 'max:256'],
            'tre_obs_professor' => ['nullable', 'string', 'max:5000'],
            'tre_publico' => ['boolean'],
            'alu_hashid' => ['nullable', 'string', 'size:6'],
        ]);
        $alunoId = ! empty($dados['alu_hashid']) ? Aluno::hashid($dados['alu_hashid'])->firstOrFail()->alu_id : null;

        $treino = Treino::create([
            'tre_titulo' => $dados['tre_titulo'],
            'tre_obs_professor' => $dados['tre_obs_professor'] ?? null,
            'tre_publico' => $alunoId ? false : (bool) ($dados['tre_publico'] ?? false),
            'alu_id' => $alunoId,
            'tre_data_cadastro' => now(),
            'tre_situacao' => 'AT',
            'tre_ordem' => (int) Treino::where('alu_id', $alunoId)->max('tre_ordem') + 1,
        ]);

        return response()->json(FichaFormato::completa($treino), 201);
    }

    public function update(Request $request, string $treHashid): JsonResponse
    {
        $treino = Treino::hashid($treHashid)->firstOrFail();
        $dados = $request->validate([
            'tre_titulo' => ['sometimes', 'string', 'max:256'],
            'tre_obs_professor' => ['nullable', 'string', 'max:5000'],
            'tre_publico' => ['sometimes', 'boolean'],
            'tre_situacao' => ['sometimes', 'in:AT,IN'],
        ]);
        if ($treino->alu_id) {
            unset($dados['tre_publico']); // ficha de aluno nunca é pública
        }
        $treino->update($dados);

        return response()->json(FichaFormato::completa($treino));
    }

    public function destroy(string $treHashid): JsonResponse
    {
        Treino::hashid($treHashid)->firstOrFail()->delete();

        return response()->json(['message' => 'Ficha excluída.']);
    }

    /** Copia uma ficha (ex.: genérica) para um aluno, com todos os exercícios (como o copiarSugestaoTreino do 69fit). */
    public function copiar(Request $request, string $treHashid): JsonResponse
    {
        $origem = Treino::with('exercicios')->hashid($treHashid)->firstOrFail();
        $aluno = Aluno::hashid($request->validate(['alu_hashid' => ['required', 'string', 'size:6']])['alu_hashid'])->firstOrFail();

        $nova = DB::transaction(function () use ($origem, $aluno) {
            $nova = Treino::create([
                'tre_titulo' => $origem->tre_titulo,
                'tre_obs_professor' => $origem->tre_obs_professor,
                'tre_publico' => false,
                'alu_id' => $aluno->alu_id,
                'tre_data_cadastro' => now(),
                'tre_situacao' => 'AT',
                'tre_ordem' => (int) Treino::where('alu_id', $aluno->alu_id)->max('tre_ordem') + 1,
            ]);
            foreach ($origem->exercicios as $tex) {
                $nova->exercicios()->create($tex->only(['exe_id', 'mus_id', 'pau_id', 'cad_id', 'met_id', 'tex_ordem', 'tex_serie', 'tex_repeticao', 'tex_carga', 'tex_tempo', 'tex_obs', 'tex_grupo_cor']));
            }

            return $nova;
        });

        return response()->json(FichaFormato::completa($nova), 201);
    }

    /** Recebe a lista de tre_hashid na nova ordem. */
    public function ordenar(Request $request): JsonResponse
    {
        $hashids = $request->validate(['ordem' => ['required', 'array'], 'ordem.*' => ['string', 'size:6']])['ordem'];
        foreach (array_values($hashids) as $i => $hashid) {
            Treino::hashid($hashid)->update(['tre_ordem' => $i + 1]);
        }

        return response()->json(['message' => 'Ordem salva.']);
    }

    public function adicionarExercicio(Request $request, string $treHashid): JsonResponse
    {
        $treino = Treino::hashid($treHashid)->firstOrFail();
        $dados = $this->dadosExercicio($request, true);
        $dados['tex_ordem'] = (int) $treino->exercicios()->max('tex_ordem') + 1;
        $tex = $treino->exercicios()->create($dados);

        return response()->json(FichaFormato::item($tex->fresh(['exercicio.musculo', 'musculo', 'metodo', 'cadencia', 'pausa'])), 201);
    }

    public function atualizarExercicio(Request $request, string $texHashid): JsonResponse
    {
        $tex = TreinoExercicio::hashid($texHashid)->firstOrFail();
        $tex->update($this->dadosExercicio($request, false));

        return response()->json(FichaFormato::item($tex->fresh(['exercicio.musculo', 'musculo', 'metodo', 'cadencia', 'pausa'])));
    }

    public function removerExercicio(string $texHashid): JsonResponse
    {
        TreinoExercicio::hashid($texHashid)->firstOrFail()->delete();

        return response()->json(['message' => 'Exercício removido da ficha.']);
    }

    /** Recebe a lista de tex_hashid da ficha na nova ordem. */
    public function ordenarExercicios(Request $request, string $treHashid): JsonResponse
    {
        $treino = Treino::hashid($treHashid)->firstOrFail();
        $hashids = $request->validate(['ordem' => ['required', 'array'], 'ordem.*' => ['string', 'size:6']])['ordem'];
        foreach (array_values($hashids) as $i => $hashid) {
            TreinoExercicio::where('tre_id', $treino->tre_id)->hashid($hashid)->update(['tex_ordem' => $i + 1]);
        }

        return response()->json(['message' => 'Ordem salva.']);
    }

    /** Campos do exercício na ficha; as referências chegam como hashid. */
    private function dadosExercicio(Request $request, bool $novo): array
    {
        $r = $novo ? 'required' : 'sometimes';
        $dados = $request->validate([
            'exe_hashid' => [$r, 'string', 'size:6'],
            'met_hashid' => ['nullable', 'string', 'size:6'],
            'cad_hashid' => ['nullable', 'string', 'size:6'],
            'pau_hashid' => ['nullable', 'string', 'size:6'],
            'tex_serie' => ['nullable', 'string', 'max:10'],
            'tex_repeticao' => ['nullable', 'string', 'max:10'],
            'tex_carga' => ['nullable', 'string', 'max:10'],
            'tex_tempo' => ['nullable', 'string', 'max:10'],
            'tex_obs' => ['nullable', 'string', 'max:2000'],
            'tex_grupo_cor' => ['nullable', 'string', 'max:10'],
        ]);

        $refs = ['exe_hashid' => [Exercicio::class, 'exe_id'], 'met_hashid' => [Metodo::class, 'met_id'], 'cad_hashid' => [Cadencia::class, 'cad_id'], 'pau_hashid' => [Pausa::class, 'pau_id']];
        foreach ($refs as $campo => [$classe, $id]) {
            if (array_key_exists($campo, $dados)) {
                $dados[$id] = $dados[$campo] ? $classe::hashid($dados[$campo])->firstOrFail()->getKey() : null;
                unset($dados[$campo]);
            }
        }
        if (isset($dados['exe_id'])) {
            $dados['mus_id'] = Exercicio::find($dados['exe_id'])->mus_id;
        }

        return $dados;
    }
}
