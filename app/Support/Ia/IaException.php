<?php

namespace App\Support\Ia;

use Illuminate\Http\JsonResponse;
use RuntimeException;

/** Falha da IA com mensagem pronta para o personal (vira 422 com {message}). */
class IaException extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
