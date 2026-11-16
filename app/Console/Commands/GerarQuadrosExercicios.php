<?php

namespace App\Console\Commands;

use App\Models\Exercicio;
use App\Support\QuadrosExercicio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Gera os quadros JPG dos GIFs dos exercícios para o PDF das fichas (ver QuadrosExercicio). Pula os já gerados; com
 * --todos refaz tudo. Precisa do ffmpeg — onde não houver (ex.: servidor), gere local e mande a pasta por rsync.
 */
class GerarQuadrosExercicios extends Command
{
    protected $signature = 'exercicios:quadros {--todos : refaz também os que já existem}';

    protected $description = 'Gera os quadros JPG (1º e 2º segundo) dos GIFs dos exercícios, usados no PDF das fichas';

    public function handle(): int
    {
        if (! QuadrosExercicio::temFfmpeg()) {
            $this->error('ffmpeg/ffprobe não encontrados neste ambiente.');

            return self::FAILURE;
        }

        $origens = Exercicio::where('exe_situacao', 'AT')->get()->map(fn ($e) => QuadrosExercicio::origem($e))->filter()->unique()->values();
        $gerados = $pulados = $falhas = 0;
        $barra = $this->output->createProgressBar($origens->count());
        foreach ($origens as $origem) {
            $barra->advance();
            if (! $this->option('todos') && QuadrosExercicio::existentes($origem)) {
                $pulados++;

                continue;
            }
            if ($this->option('todos')) {
                File::delete(QuadrosExercicio::existentes($origem));
            }
            QuadrosExercicio::gerar($origem) ? $gerados++ : $falhas++;
        }
        $barra->finish();
        $this->newLine();
        $this->info("{$gerados} gerado(s), {$pulados} já existiam, {$falhas} falha(s).");

        return $falhas ? self::FAILURE : self::SUCCESS;
    }
}
