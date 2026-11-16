<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Treino;
use App\Models\TreinoExercicio;
use App\Support\QuadrosExercicio;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * PDF das fichas (uma ou todas as do aluno), no visual do app: preto, verde-limão e Montserrat. Cada exercício sai
 * com os 2 quadros do GIF lado a lado (QuadrosExercicio). O dompdf lê imagens e fontes do disco — nada remoto.
 */
class FichaPdfService
{
    /** @param  Collection<int, Treino>  $fichas */
    public function gerar(Collection $fichas, ?Aluno $aluno = null): string
    {
        $fichas->each->loadMissing(['aluno', 'exercicios.exercicio.musculo', 'exercicios.musculo', 'exercicios.metodo', 'exercicios.cadencia', 'exercicios.pausa']);

        $html = view('pdf.fichas', [
            'fichas' => $fichas->map(fn (Treino $t) => [
                'titulo' => $t->tre_titulo,
                'orientacoes' => $t->tre_obs_professor,
                'data' => $t->tre_data_cadastro?->format('d/m/Y'),
                'exercicios' => $t->exercicios->map(fn (TreinoExercicio $tex) => $this->exercicio($tex))->all(),
            ])->all(),
            'aluno' => ($aluno ?? $fichas->first()?->aluno)?->alu_nome,
            'fonte' => resource_path('fonts'),
            'geradoEm' => now()->format('d/m/Y'),
        ])->render();

        File::ensureDirectoryExists(storage_path('fonts'));
        $opcoes = new Options([
            'chroot' => [resource_path('fonts'), storage_path('app/public'), public_path()],
            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'tempDir' => sys_get_temp_dir(),
            'isRemoteEnabled' => false,
            'defaultFont' => 'Montserrat',
            'dpi' => 144,
        ]);
        $pdf = new Dompdf($opcoes);
        $pdf->setPaper('A4');
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->render();

        // nº da página no canto do rodapé (o dompdf não conhece counter(pages) no CSS)
        $fonte = $pdf->getFontMetrics()->getFont('Montserrat');
        $pdf->getCanvas()->page_text(515, 804.5, 'página {PAGE_NUM} de {PAGE_COUNT}', $fonte, 6.5, [0.54, 0.55, 0.58]);

        return $pdf->output();
    }

    /** "Ficha - Camila Rocha - Treino A.pdf" */
    public function nomeArquivo(Collection $fichas, ?Aluno $aluno = null): string
    {
        $partes = array_filter(['Ficha', ($aluno ?? $fichas->first()?->aluno)?->alu_nome, $fichas->count() === 1 ? $fichas->first()->tre_titulo : null]);

        return Str::of(implode(' - ', $partes))->ascii()->replaceMatches('/[^A-Za-z0-9 .\-]+/', ' ')->squish().'.pdf';
    }

    private function exercicio(TreinoExercicio $tex): array
    {
        $exe = $tex->exercicio;

        return [
            'nome' => $exe?->exe_exercicio ?? 'Exercício',
            'musculo' => ($tex->musculo ?? $exe?->musculo)?->mus_musculo,
            'quadros' => $exe ? QuadrosExercicio::caminhos($exe) : [],
            'series' => $tex->tex_serie,
            'repeticoes' => $tex->tex_repeticao,
            'detalhes' => array_filter([
                'Carga' => $tex->tex_carga,
                'Tempo' => $tex->tex_tempo,
                'Descanso' => $tex->pausa?->pau_pausa,
                'Método' => $tex->metodo?->met_metodo,
                'Cadência' => $tex->cadencia?->cad_cadencia,
            ], fn ($v) => $v !== null && $v !== '' && $v !== '-'),
            'obs' => $tex->tex_obs,
            'cor' => preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $tex->tex_grupo_cor) ? $tex->tex_grupo_cor : null,
        ];
    }
}
