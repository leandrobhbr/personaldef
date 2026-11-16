<?php

namespace App\Http\Controllers\Aplicativo\Personal;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Services\FichaIaService;
use App\Support\FichaFormato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * "Fichas com IA" (tela do aluno): o personal escreve o pedido, a IA sugere as fichas e ele confirma uma por uma.
 */
class FichaIaController extends Controller
{
    public function __construct(private FichaIaService $service) {}

    /** Só sugere — nada é gravado aqui. */
    public function sugerir(Request $request): JsonResponse
    {
        $pedido = trim($request->validate(['pedido' => ['required', 'string', 'min:10', 'max:6000']])['pedido']);
        set_time_limit(180);

        $proposta = $this->service->sugerir($pedido);
        Log::info('Fichas com IA: '.count($proposta['fichas']).' ficha(s) sugerida(s)', $proposta['uso']);

        return response()->json($proposta);
    }

    /** Cria uma ficha conferida (do aluno; sem alu_hashid, genérica). */
    public function confirmar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'alu_hashid' => ['nullable', 'string', 'size:6'],
            'tre_titulo' => ['required', 'string', 'max:256'],
            'tre_obs_professor' => ['nullable', 'string', 'max:5000'],
            'exercicios' => ['required', 'array', 'min:1', 'max:40'],
            'exercicios.*.exe_hashid' => ['required', 'string', 'size:6'],
            'exercicios.*.met_hashid' => ['nullable', 'string', 'size:6'],
            'exercicios.*.cad_hashid' => ['nullable', 'string', 'size:6'],
            'exercicios.*.pau_hashid' => ['nullable', 'string', 'size:6'],
            'exercicios.*.tex_serie' => ['nullable', 'string', 'max:10'],
            'exercicios.*.tex_repeticao' => ['nullable', 'string', 'max:10'],
            'exercicios.*.tex_carga' => ['nullable', 'string', 'max:10'],
            'exercicios.*.tex_tempo' => ['nullable', 'string', 'max:10'],
            'exercicios.*.tex_obs' => ['nullable', 'string', 'max:2000'],
        ], [
            'exercicios.required' => 'A ficha precisa de pelo menos um exercício.',
            'exercicios.*.exe_hashid.required' => 'Escolha o exercício do catálogo em todos os itens.',
        ]);
        $aluno = ! empty($dados['alu_hashid']) ? Aluno::hashid($dados['alu_hashid'])->firstOrFail() : null;

        return response()->json(FichaFormato::completa($this->service->confirmar($aluno, $dados)), 201);
    }
}
