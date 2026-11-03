<?php

namespace App\Http\Middleware;

use App\Models\Acesso;
use App\Support\LinkMagico;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identifica o aparelho pelo token (Authorization: Bearer). Uso: 'acesso.app' (aluno ou personal)
 * ou 'acesso.app:personal' (só o Denilson). O acesso fica em $request->attributes->get('acesso').
 */
class AcessoAplicativo
{
    public function handle(Request $request, Closure $next, ?string $perfil = null): Response
    {
        $token = $request->bearerToken();
        $acesso = $token ? Acesso::query()
            ->where('ace_token_hash', LinkMagico::hash($token))
            ->whereNull('ace_revogado_em')
            ->with('aluno')
            ->first() : null;

        if (! $acesso || ($acesso->alu_id && $acesso->aluno?->alu_situacao !== 'AT')) {
            return response()->json(['message' => 'Acesso não liberado neste aparelho.'], 401);
        }
        if ($perfil === 'personal' && ! $acesso->ehPersonal()) {
            return response()->json(['message' => 'Área exclusiva do personal.'], 403);
        }

        // registra o último uso no máximo a cada 10 minutos
        if (! $acesso->ace_ultimo_uso || $acesso->ace_ultimo_uso->lt(now()->subMinutes(10))) {
            $acesso->forceFill(['ace_ultimo_uso' => now()])->saveQuietly();
        }

        $request->attributes->set('acesso', $acesso);

        return $next($request);
    }
}
