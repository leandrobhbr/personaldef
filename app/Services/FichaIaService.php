<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Cadencia;
use App\Models\Exercicio;
use App\Models\Metodo;
use App\Models\Musculo;
use App\Models\Pausa;
use App\Models\Treino;
use App\Support\FichaFormato;
use App\Support\Ia\SugeridorFichas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * "Fichas com IA": a IA (SugeridorFichas) só lê o pedido do personal e escolhe ids do catálogo. Aqui os ids são
 * conferidos no banco (id inexistente vira "escolha o exercício" na tela) e a resposta vira uma proposta de fichas que
 * o personal confere, aba por aba. Nada é gravado antes de confirmar(), que cria uma ficha por vez.
 */
class FichaIaService
{
    /** tamanho das colunas tex_serie, tex_repeticao, tex_carga e tex_tempo */
    private const CURTO = 10;

    public function __construct(private SugeridorFichas $sugeridor) {}

    public function sugerir(string $pedido): array
    {
        $lido = $this->sugeridor->sugerir($this->catalogo(), $pedido);
        ['tokens_entrada' => $entrada, 'tokens_saida' => $saida] = $lido['uso'];

        return $this->montar($lido['dados']) + ['uso' => [
            'tokens_entrada' => $entrada,
            'tokens_saida' => $saida,
            'custo_usd' => round(($entrada * config('services.anthropic.preco_entrada_mtok')
                + $saida * config('services.anthropic.preco_saida_mtok')) / 1_000_000, 4),
        ]];
    }

    /**
     * Catálogo que vai para a IA: um exercício por linha ("266 Supino máquina 45º"), agrupado por músculo, e as listas
     * de métodos, cadências e descansos. Sempre na mesma ordem, para o cache da API reaproveitar entre pedidos.
     */
    public function catalogo(): string
    {
        $linhas = [];
        $exercicios = Exercicio::where('exe_situacao', 'AT')->orderBy('exe_exercicio')->orderBy('exe_id')->get(['exe_id', 'exe_exercicio', 'mus_id'])->groupBy('mus_id');
        foreach (Musculo::where('mus_situacao', 'AT')->orderBy('mus_musculo')->get() as $m) {
            if ($lista = $exercicios->get($m->mus_id)) {
                $linhas[] = "## Exercícios — {$m->mus_musculo}";
                foreach ($lista as $e) {
                    // alguns nomes vieram do 69fit com ".gif" no fim
                    $linhas[] = $e->exe_id.' '.preg_replace('/\.gif$/i', '', trim($e->exe_exercicio));
                }
                $linhas[] = '';
            }
        }
        foreach (['Métodos (metodo_id)' => [Metodo::class, 'met'], 'Cadências (cadencia_id)' => [Cadencia::class, 'cad'], 'Descansos (descanso_id)' => [Pausa::class, 'pau']] as $titulo => [$classe, $p]) {
            $linhas[] = "## {$titulo}";
            foreach ($this->opcoes($classe, $p) as $o) {
                $linhas[] = $o->getKey().' '.$o->{$p.'_'.$this->sufixo($p)};
            }
            $linhas[] = '';
        }

        return trim(implode("\n", $linhas));
    }

    /** O que a IA devolveu → proposta com os exercícios do banco (mídia, músculo) para a tela de conferência. */
    public function montar(array $d): array
    {
        $ids = collect($d['fichas'] ?? [])->flatMap(fn ($f) => collect($f['exercicios'] ?? [])
            ->flatMap(fn ($e) => array_merge([$e['exercicio_id'] ?? null], $e['alternativas'] ?? [])))
            ->filter(fn ($id) => is_int($id))->unique();
        $exercicios = Exercicio::with('musculo')->where('exe_situacao', 'AT')->whereIn('exe_id', $ids)->get()->keyBy('exe_id');
        $metodos = $this->opcoes(Metodo::class, 'met')->keyBy('met_id');
        $cadencias = $this->opcoes(Cadencia::class, 'cad')->keyBy('cad_id');
        $pausas = $this->opcoes(Pausa::class, 'pau')->keyBy('pau_id');

        $fichas = [];
        foreach (array_slice($d['fichas'] ?? [], 0, 8) as $i => $f) {
            $itens = [];
            foreach (array_slice($f['exercicios'] ?? [], 0, 30) as $j => $e) {
                $exe = $exercicios->get($e['exercicio_id'] ?? null);
                $obs = trim((string) ($e['observacao'] ?? ''));
                $campos = [];
                foreach (['series' => 'tex_serie', 'repeticoes' => 'tex_repeticao', 'carga' => 'tex_carga', 'tempo' => 'tex_tempo'] as $de => $para) {
                    $valor = trim((string) ($e[$de] ?? ''));
                    // não cabe na coluna: vai inteiro para a observação e fica cortado no campo
                    if (mb_strlen($valor) > self::CURTO) {
                        $obs = trim("{$obs} ".ucfirst($de).": {$valor}.");
                        $valor = mb_substr($valor, 0, self::CURTO);
                    }
                    $campos[$para] = $valor !== '' ? $valor : null;
                }

                $itens[] = [
                    'chave' => 'F'.($i + 1).'E'.($j + 1),
                    'nome_pedido' => trim((string) ($e['nome_pedido'] ?? '')),
                    'exercicio' => $exe ? FichaFormato::exercicio($exe) : null,
                    'alternativas' => collect($e['alternativas'] ?? [])->unique()->reject(fn ($id) => $id === $exe?->exe_id)
                        ->map(fn ($id) => $exercicios->get($id))->filter()->take(3)->map(fn ($a) => FichaFormato::exercicio($a))->values()->all(),
                    'pausa' => $this->opcao($pausas->get($e['descanso_id'] ?? null), 'pau'),
                    'metodo' => $this->opcao($metodos->get($e['metodo_id'] ?? null), 'met'),
                    'cadencia' => $this->opcao($cadencias->get($e['cadencia_id'] ?? null), 'cad'),
                    'tex_obs' => $obs !== '' ? $obs : null,
                ] + $campos;
            }

            $fichas[] = [
                'chave' => 'F'.($i + 1),
                'titulo' => mb_substr(trim((string) ($f['titulo'] ?? '')) ?: 'Treino '.chr(65 + $i), 0, 256),
                'orientacoes' => trim((string) ($f['orientacoes'] ?? '')) ?: null,
                'exercicios' => $itens,
            ];
        }

        return [
            'resumo' => trim((string) ($d['resumo'] ?? '')),
            'avisos' => array_values(array_filter(array_map('trim', $d['avisos'] ?? []))),
            'fichas' => $fichas,
        ];
    }

    /** Cria UMA ficha conferida pelo personal (do aluno, ou genérica se $aluno for null), com os exercícios na ordem. */
    public function confirmar(?Aluno $aluno, array $dados): Treino
    {
        $refs = ['exe_hashid' => Exercicio::class, 'met_hashid' => Metodo::class, 'cad_hashid' => Cadencia::class, 'pau_hashid' => Pausa::class];
        $linhas = [];
        foreach ($dados['exercicios'] as $i => $e) {
            $linha = array_intersect_key($e, array_flip(['tex_serie', 'tex_repeticao', 'tex_carga', 'tex_tempo', 'tex_obs'])) + ['tex_ordem' => $i + 1];
            foreach ($refs as $campo => $classe) {
                if (! empty($e[$campo])) {
                    $modelo = $classe::hashid($e[$campo])->firstOrFail();
                    $linha[$modelo->getKeyName()] = $modelo->getKey();
                    if ($modelo instanceof Exercicio) {
                        $linha['mus_id'] = $modelo->mus_id;
                    }
                }
            }
            $linhas[] = $linha;
        }

        return DB::transaction(function () use ($aluno, $dados, $linhas) {
            $alunoId = $aluno?->alu_id;
            $treino = Treino::create([
                'tre_titulo' => $dados['tre_titulo'],
                'tre_obs_professor' => $dados['tre_obs_professor'] ?? null,
                'tre_publico' => false,
                'alu_id' => $alunoId,
                'tre_data_cadastro' => now(),
                'tre_situacao' => 'AT',
                'tre_ordem' => (int) Treino::where('alu_id', $alunoId)->max('tre_ordem') + 1,
            ]);
            foreach ($linhas as $linha) {
                $treino->exercicios()->create($linha);
            }

            return $treino;
        });
    }

    /** Métodos, cadências e descansos ativos, sem a opção "-" (vazio) que veio do 69fit. */
    private function opcoes(string $classe, string $p): Collection
    {
        $campo = $p.'_'.$this->sufixo($p);

        return $classe::where($p.'_situacao', 'AT')->where($campo, '<>', '-')->orderBy($p.'_id')->get();
    }

    private function opcao($modelo, string $p): ?array
    {
        return $modelo ? ['hashid' => $modelo->{$p.'_hashid'}, 'nome' => $modelo->{$p.'_'.$this->sufixo($p)}] : null;
    }

    private function sufixo(string $p): string
    {
        return ['met' => 'metodo', 'cad' => 'cadencia', 'pau' => 'pausa'][$p];
    }
}
