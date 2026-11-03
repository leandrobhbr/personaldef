<?php

namespace App\Http\Controllers\Aplicativo;

use App\Http\Controllers\Controller;
use App\Models\Acesso;
use App\Support\LinkMagico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Libera o aparelho pelo link mágico (ou código de 6 dígitos) e informa quem está usando o app.
 */
class AcessoController extends Controller
{
    public function entrar(Request $request): JsonResponse
    {
        $dados = $request->validate(['token' => ['required', 'string', 'max:100']]);
        $resultado = LinkMagico::trocar(trim($dados['token']), $request->userAgent());

        if (! $resultado) {
            return response()->json(['message' => 'Link ou código inválido, já usado ou vencido. Peça um novo ao Denilson.'], 422);
        }

        return response()->json(['token' => $resultado['token']] + $this->perfil($resultado['acesso']));
    }

    public function eu(Request $request): JsonResponse
    {
        return response()->json($this->perfil($request->attributes->get('acesso')));
    }

    public function sair(Request $request): JsonResponse
    {
        $request->attributes->get('acesso')->forceFill(['ace_revogado_em' => now()])->save();

        return response()->json(['message' => 'Aparelho desconectado.']);
    }

    private function perfil(Acesso $acesso): array
    {
        $acesso->loadMissing('aluno');

        return [
            'perfil' => $acesso->ehPersonal() ? 'personal' : 'aluno',
            'aluno' => $acesso->aluno ? ['alu_hashid' => $acesso->aluno->alu_hashid, 'alu_nome' => $acesso->aluno->alu_nome] : null,
        ];
    }
}
