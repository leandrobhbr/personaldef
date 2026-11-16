<?php

namespace App\Support;

use App\Models\Exercicio;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

/**
 * Quadros JPG dos exercícios para o PDF da ficha: do GIF saem 2 quadros (no 1º e no 2º segundo, lado a lado no PDF);
 * de imagem parada (png/webp) sai 1. Ficam em storage/app/public/exercicios/quadros/{nome do arquivo}-{n}.jpg e são
 * gerados pelo ffmpeg (php artisan exercicios:quadros), uma vez só.
 */
class QuadrosExercicio
{
    public const LARGURA = 400;

    private static ?bool $temFfmpeg = null;

    public static function pasta(): string
    {
        return storage_path('app/public/exercicios/quadros');
    }

    /** Arquivo de origem do exercício no disco: o GIF; sem GIF, a imagem. null se não houver arquivo local. */
    public static function origem(Exercicio $exe): ?string
    {
        foreach ([$exe->exe_gif, $exe->exe_imagem] as $arquivo) {
            if (! $arquivo || str_starts_with($arquivo, 'http')) {
                continue;
            }
            $caminho = str_starts_with($arquivo, '/') ? public_path(ltrim($arquivo, '/')) : storage_path('app/public/exercicios/'.$arquivo);
            if (is_file($caminho)) {
                return $caminho;
            }
        }

        return null;
    }

    /** JPGs prontos do exercício (0, 1 ou 2). Se faltarem e o ffmpeg existir aqui, gera na hora. */
    public static function caminhos(Exercicio $exe): array
    {
        $origem = self::origem($exe);
        if (! $origem) {
            return [];
        }
        if ($prontos = self::existentes($origem)) {
            return $prontos;
        }

        // sem quadros: tenta gerar na hora; se falhar (sem permissão na pasta, ffmpeg com erro), o PDF sai sem imagem
        try {
            return self::temFfmpeg() ? self::gerar($origem) : [];
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }

    /** @return list<string> */
    public static function existentes(string $origem): array
    {
        $base = self::pasta().'/'.pathinfo($origem, PATHINFO_FILENAME);

        return array_values(array_filter([$base.'-1.jpg', $base.'-2.jpg'], 'is_file'));
    }

    /**
     * Gera os quadros com o ffmpeg. GIF: no 1º e no 2º segundo (GIF com menos de ~2,2 s: em 1/3 e 2/3 da duração).
     * A decodificação é do início até o ponto (-ss depois do -i): com -ss antes, GIFs que só redesenham parte da tela
     * a cada quadro saíam em branco. Transparência vira fundo branco.
     *
     * @return list<string>
     */
    public static function gerar(string $origem): array
    {
        File::ensureDirectoryExists(self::pasta());
        $base = self::pasta().'/'.pathinfo($origem, PATHINFO_FILENAME);
        $filtro = 'scale='.self::LARGURA.':-2:flags=lanczos,format=rgba,split[a][b];[a]drawbox=t=fill:c=white[fundo];[fundo][b]overlay,format=yuvj420p';

        // GIF de um quadro só (alguns do catálogo são imagem parada) sai como imagem: 1 JPG
        $tempos = [null];
        if (strtolower(pathinfo($origem, PATHINFO_EXTENSION)) === 'gif') {
            // saída: nº de quadros numa linha, duração em segundos na outra
            [$quadros, $duracao] = array_map('floatval', array_pad(preg_split('/\s+/', trim(Process::run(['ffprobe', '-v', 'error',
                '-select_streams', 'v:0', '-show_entries', 'stream=nb_frames:format=duration', '-of', 'csv=p=0', $origem])->output())), 2, '0'));
            if ($quadros > 1) {
                $tempos = $duracao >= 2.2 ? [1.0, 2.0] : [round($duracao / 3, 2), round($duracao * 2 / 3, 2)];
            }
        }

        return self::extrair($origem, $base, $filtro, $tempos) ?: self::extrair($origem, $base, $filtro, [null]);
    }

    /** @return list<string> */
    private static function extrair(string $origem, string $base, string $filtro, array $tempos): array
    {
        $gerados = [];
        foreach ($tempos as $i => $t) {
            $destino = $base.'-'.($i + 1).'.jpg';
            $cmd = ['ffmpeg', '-v', 'error', '-y', '-i', $origem];
            if ($t) {
                array_push($cmd, '-ss', (string) $t);
            }
            array_push($cmd, '-frames:v', '1', '-filter_complex', $filtro, '-q:v', '4', $destino);
            if (Process::timeout(60)->run($cmd)->successful() && is_file($destino) && filesize($destino) > 0) {
                $gerados[] = $destino;
            }
        }

        return $gerados;
    }

    public static function temFfmpeg(): bool
    {
        return self::$temFfmpeg ??= Process::run(['sh', '-c', 'command -v ffmpeg && command -v ffprobe'])->successful();
    }
}
