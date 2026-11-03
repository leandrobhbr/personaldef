<?php

namespace App\Http\Controllers\Aplicativo;

use App\Http\Controllers\Controller;
use App\Models\Acesso;
use App\Models\PushAssinatura;
use App\Support\LinkMagico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Inscrição do aparelho para o aviso de nova versão (Web Push). Vale com ou sem link mágico.
 */
class PushController extends Controller
{
    public function chave(): JsonResponse
    {
        return response()->json(['chave' => config('aplicativo.vapid.public_key')]);
    }

    public function assinar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'endpoint' => ['required', 'url', 'max:500'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
        ]);
        $token = $request->bearerToken();
        $acesso = $token ? Acesso::where('ace_token_hash', LinkMagico::hash($token))->whereNull('ace_revogado_em')->first() : null;

        PushAssinatura::updateOrCreate(['pus_endpoint' => $dados['endpoint']], [
            'pus_p256dh' => $dados['keys']['p256dh'],
            'pus_auth' => $dados['keys']['auth'],
            'ace_id' => $acesso?->ace_id,
        ]);

        return response()->json(['message' => 'Avisos ativados.']);
    }

    public function cancelar(Request $request): JsonResponse
    {
        PushAssinatura::where('pus_endpoint', $request->validate(['endpoint' => ['required', 'string']])['endpoint'])->delete();

        return response()->json(['message' => 'Avisos desativados.']);
    }
}
