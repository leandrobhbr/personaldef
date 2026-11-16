<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Treino;
use App\Services\FichaPdfService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** PDF de uma ficha, ou de todas as fichas ativas do aluno num arquivo só. ?modelo=simplificado: só tabelas, sem imagens. */
class FichaPdfController extends Controller
{
    public function __construct(private FichaPdfService $pdf) {}

    public function ficha(Request $request, string $treHashid): Response
    {
        return $this->pdf->resposta($request, collect([Treino::hashid($treHashid)->firstOrFail()]));
    }

    public function aluno(Request $request, string $aluHashid): Response
    {
        $aluno = Aluno::hashid($aluHashid)->firstOrFail();
        $fichas = Treino::where('alu_id', $aluno->alu_id)->where('tre_situacao', 'AT')->orderBy('tre_ordem')->get();
        abort_if($fichas->isEmpty(), 404, 'Este aluno ainda não tem fichas.');

        return $this->pdf->resposta($request, $fichas, $aluno);
    }
}
