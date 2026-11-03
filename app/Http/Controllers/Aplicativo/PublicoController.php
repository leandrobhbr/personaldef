<?php

namespace App\Http\Controllers\Aplicativo;

use App\Http\Controllers\Controller;
use App\Models\Treino;
use App\Support\FichaFormato;
use Illuminate\Http\JsonResponse;

/**
 * Área livre do aplicativo (sem link mágico): fichas genéricas que o Denilson deixou públicas.
 */
class PublicoController extends Controller
{
    public function fichas(): JsonResponse
    {
        $fichas = Treino::publicas()->withCount('exercicios')->orderBy('tre_ordem')->get();

        return response()->json([
            'aviso' => config('aplicativo.aviso_ficha_personalizada'),
            'fichas' => $fichas->map(fn ($t) => FichaFormato::resumo($t))->values(),
        ]);
    }

    public function ficha(string $treHashid): JsonResponse
    {
        $treino = Treino::publicas()->hashid($treHashid)->firstOrFail();

        return response()->json(['aviso' => config('aplicativo.aviso_ficha_personalizada')] + FichaFormato::completa($treino));
    }
}
