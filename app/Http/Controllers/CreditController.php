<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreditController extends Controller
{
    public function apply(Request $request, $id)
    {
        // 1. Validação do valor (Erro 422 se <= 0)
        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ], [
            'amount.gt' => 'O valor do crédito deve ser positivo e maior que zero.',
        ]);

        // 2. Apenas role 'financial' (e admin pelo bypass do Gate)
        // Usamos o middleware na rota, mas aqui podemos reforçar se necessário
        if (!Auth::user()->hasPermissionTo('manual credit')) {
             return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $client = Client::findOrFail($id);
        $oldBalance = $client->credit_balance;
        $creditAmount = $request->amount;

        try {
            DB::transaction(function () use ($client, $creditAmount) {
                // Atualiza o saldo do cliente
                $client->increment('credit_balance', $creditAmount);
            });

            $newBalance = $client->fresh()->credit_balance;

            // 3. Registrar no Log (requisito obrigatório)
            Log::info("Crédito Manual Aplicado", [
                'operator_id' => Auth::id(),
                'operator_name' => Auth::user()->name,
                'client_id' => $client->id,
                'amount_added' => $creditAmount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Crédito aplicado com sucesso.',
                'data' => [
                    'previous_balance' => $oldBalance,
                    'current_balance' => $newBalance
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Falha ao aplicar crédito.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
