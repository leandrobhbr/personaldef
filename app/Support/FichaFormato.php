<?php

namespace App\Support;

use App\Models\Exercicio;
use App\Models\Treino;
use App\Models\TreinoExercicio;

/**
 * Formato das fichas nas APIs do aplicativo (só hashids, nunca ids numéricos).
 */
class FichaFormato
{
    public static function resumo(Treino $treino): array
    {
        return [
            'tre_hashid' => $treino->tre_hashid,
            'tre_titulo' => $treino->tre_titulo,
            'tre_obs_professor' => $treino->tre_obs_professor,
            'tre_publico' => (bool) $treino->tre_publico,
            'tre_situacao' => $treino->tre_situacao,
            'tre_ordem' => $treino->tre_ordem,
            'tre_data_cadastro' => $treino->tre_data_cadastro?->format('d/m/Y H:i'),
            'qtd_exercicios' => $treino->exercicios_count ?? $treino->exercicios()->count(),
        ];
    }

    public static function completa(Treino $treino): array
    {
        $treino->loadMissing(['aluno', 'exercicios.exercicio.musculo', 'exercicios.musculo', 'exercicios.metodo', 'exercicios.cadencia', 'exercicios.pausa']);

        return self::resumo($treino) + [
            // topo da ficha exclusiva: nome do aluno e data/hora de criação
            'aluno' => $treino->aluno ? ['alu_hashid' => $treino->aluno->alu_hashid, 'alu_nome' => $treino->aluno->alu_nome] : null,
            'exercicios' => $treino->exercicios->map(fn (TreinoExercicio $tex) => self::item($tex))->values(),
        ];
    }

    public static function item(TreinoExercicio $tex): array
    {
        $nome = fn ($rel, $campo) => $rel ? ['hashid' => $rel->{substr($campo, 0, 3).'_hashid'}, 'nome' => $rel->{$campo}, 'descricao' => $rel->{substr($campo, 0, 3).'_descricao'}] : null;

        return [
            'tex_hashid' => $tex->tex_hashid,
            'tex_ordem' => $tex->tex_ordem,
            'tex_serie' => $tex->tex_serie,
            'tex_repeticao' => $tex->tex_repeticao,
            'tex_carga' => $tex->tex_carga,
            'tex_tempo' => $tex->tex_tempo,
            'tex_obs' => $tex->tex_obs,
            'tex_grupo_cor' => $tex->tex_grupo_cor,
            'exercicio' => $tex->exercicio ? self::exercicio($tex->exercicio) : null,
            'musculo' => $nome($tex->musculo ?? $tex->exercicio?->musculo, 'mus_musculo'),
            'metodo' => $nome($tex->metodo, 'met_metodo'),
            'cadencia' => $nome($tex->cadencia, 'cad_cadencia'),
            'pausa' => $nome($tex->pausa, 'pau_pausa'),
        ];
    }

    public static function exercicio(Exercicio $exe): array
    {
        return [
            'exe_hashid' => $exe->exe_hashid,
            'exe_exercicio' => $exe->exe_exercicio,
            'exe_explicacao' => $exe->exe_explicacao,
            'exe_video' => $exe->exe_video,
            'exe_imagem' => self::midia($exe->exe_imagem),
            'exe_gif' => self::midia($exe->exe_gif),
            'mus_hashid' => $exe->musculo?->mus_hashid,
            'mus_musculo' => $exe->musculo?->mus_musculo,
        ];
    }

    /** Arquivo de mídia do exercício: URL completa (ou externa, se já for http). */
    public static function midia(?string $arquivo): ?string
    {
        if (! $arquivo) {
            return null;
        }

        if (str_starts_with($arquivo, 'http')) {
            return $arquivo;
        }

        // caminho do próprio site (/images/...) ou arquivo enviado (storage/exercicios)
        return str_starts_with($arquivo, '/') ? asset(ltrim($arquivo, '/')) : asset('storage/exercicios/'.$arquivo);
    }
}
