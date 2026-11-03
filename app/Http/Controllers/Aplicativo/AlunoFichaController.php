<?php

namespace App\Http\Controllers\Aplicativo;

use App\Http\Controllers\Controller;
use App\Models\Treino;
use App\Support\FichaFormato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Fichas exclusivas do aluno do aparelho. Toda consulta filtra pelo aluno do token:
 * ficha de outro aluno responde 404.
 */
class AlunoFichaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $alunoId = $request->attributes->get('acesso')->alu_id;
        $fichas = Treino::where('alu_id', $alunoId ?? 0)->where('tre_situacao', 'AT')->withCount('exercicios')->orderBy('tre_ordem')->get();

        return response()->json(['fichas' => $fichas->map(fn ($t) => FichaFormato::resumo($t))->values()]);
    }

    public function show(Request $request, string $treHashid): JsonResponse
    {
        $alunoId = $request->attributes->get('acesso')->alu_id;
        $treino = Treino::where('alu_id', $alunoId ?? 0)->where('tre_situacao', 'AT')->hashid($treHashid)->firstOrFail();

        return response()->json(FichaFormato::completa($treino));
    }
}
