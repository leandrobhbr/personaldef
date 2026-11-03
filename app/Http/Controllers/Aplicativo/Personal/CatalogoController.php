<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Cadencia;
use App\Models\Exercicio;
use App\Models\Metodo;
use App\Models\Musculo;
use App\Models\Pausa;
use App\Support\FichaFormato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Catálogo para montar fichas: músculos com exercícios, métodos, cadências e pausas.
 */
class CatalogoController extends Controller
{
    public function index(): JsonResponse
    {
        $opcao = fn ($m, $campo) => ['hashid' => $m->{substr($campo, 0, 3).'_hashid'}, 'nome' => $m->{$campo}, 'descricao' => $m->{substr($campo, 0, 3).'_descricao'}];

        return response()->json([
            'musculos' => Musculo::where('mus_situacao', 'AT')->orderBy('mus_musculo')->get()->map(fn ($m) => $opcao($m, 'mus_musculo') + [
                'exercicios' => Exercicio::with('musculo')->where('mus_id', $m->mus_id)->where('exe_situacao', 'AT')->orderBy('exe_exercicio')->get()->map(fn ($e) => FichaFormato::exercicio($e))->values(),
            ])->values(),
            'metodos' => Metodo::where('met_situacao', 'AT')->orderBy('met_metodo')->get()->map(fn ($m) => $opcao($m, 'met_metodo'))->values(),
            'cadencias' => Cadencia::where('cad_situacao', 'AT')->orderBy('cad_cadencia')->get()->map(fn ($m) => $opcao($m, 'cad_cadencia'))->values(),
            'pausas' => Pausa::where('pau_situacao', 'AT')->orderBy('pau_pausa')->get()->map(fn ($m) => $opcao($m, 'pau_pausa'))->values(),
        ]);
    }

    public function salvarExercicio(Request $request, ?string $exeHashid = null): JsonResponse
    {
        $dados = $request->validate([
            'exe_exercicio' => [$exeHashid ? 'sometimes' : 'required', 'string', 'max:100'],
            'mus_hashid' => [$exeHashid ? 'sometimes' : 'required', 'string', 'size:6'],
            'exe_video' => ['nullable', 'url', 'max:256'],
            'exe_explicacao' => ['nullable', 'string', 'max:5000'],
            'exe_situacao' => ['sometimes', 'in:AT,IN'],
        ]);
        if (isset($dados['mus_hashid'])) {
            $dados['mus_id'] = Musculo::hashid($dados['mus_hashid'])->firstOrFail()->mus_id;
            unset($dados['mus_hashid']);
        }
        $exercicio = $exeHashid ? tap(Exercicio::hashid($exeHashid)->firstOrFail())->update($dados) : Exercicio::create($dados);

        return response()->json(FichaFormato::exercicio($exercicio->fresh('musculo')), $exeHashid ? 200 : 201);
    }
}
