<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Treino;
use App\Services\FichaPdfService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/** PDF de uma ficha, ou de todas as fichas ativas do aluno num arquivo só. */
class FichaPdfController extends Controller
{
    public function __construct(private FichaPdfService $pdf) {}

    public function ficha(string $treHashid): Response
    {
        return $this->resposta(collect([Treino::hashid($treHashid)->firstOrFail()]));
    }

    public function aluno(string $aluHashid): Response
    {
        $aluno = Aluno::hashid($aluHashid)->firstOrFail();
        $fichas = Treino::where('alu_id', $aluno->alu_id)->where('tre_situacao', 'AT')->orderBy('tre_ordem')->get();
        abort_if($fichas->isEmpty(), 404, 'Este aluno ainda não tem fichas.');

        return $this->resposta($fichas, $aluno);
    }

    private function resposta(Collection $fichas, ?Aluno $aluno = null): Response
    {
        set_time_limit(120);
        $nome = $this->pdf->nomeArquivo($fichas, $aluno);

        return response($this->pdf->gerar($fichas, $aluno), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$nome.'"',
            'Cache-Control' => 'no-store',
        ]);
    }
}
