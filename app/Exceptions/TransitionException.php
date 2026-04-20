<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Http\JsonResponse;

class TransitionException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'Erro de Regra de Negócio',
            'errors'  => [
                'domain' => $this->getMessage()
            ]
        ], 422);
    }
}
