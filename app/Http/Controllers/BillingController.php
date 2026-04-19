<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Contract;
use Exception;
use App\Enums\BillingStatus;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Buscamos as cobranças carregando o contrato e o cliente de uma só vez (Performance!)
            $billings = Billing::with(['contract.client'])->get();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Listagem de cobranças recuperada.',
                'data'   => $billings
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao listar cobranças.',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'contract_id' => 'required|exists:contracts,id',
                'due_date'    => 'required|date|after_or_equal:today',
                'total_amount'=> 'nullable|numeric|min:0',
            ]);

            $contract = Contract::findOrFail($validated['contract_id']);
            
            // Se não enviado no JSON, o valor padrão é o total do contrato
            $amount = $validated['total_amount'] ?? $contract->total_value;

            // Criando a cobrança com o Enum
            $billing = Billing::create([
                'contract_id'  => $contract->id,
                'total_amount' => $amount,
                'paid_amount'  => 0, // Começa zerado
                'due_date'     => $validated['due_date'],
                'status'       => BillingStatus::PENDING, 
            ]);

            return response()->json([
                'status' => 'success',
                'code'   => 201,
                'message' => 'Cobrança gerada com sucesso!',
                'data'   => $billing
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Erro de validação.',
                'errors'  => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Falha ao gerar cobrança.',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $billing = Billing::findOrFail($id);

            $validated = $request->validate([
                'status' => 'sometimes|string',
                'paid_amount' => 'sometimes|numeric|min:0',
                'cancellation_reason' => 'required_if:status,cancelado|string|min:10'
            ]);

            // 1. Validar Transição de Status
            if ($request->has('status')) {
                $newStatus = BillingStatus::from($validated['status']);
                
                if (!$billing->status->canTransitionTo($newStatus)) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 422,
                        'message' => "Transição de {$billing->status->value} para {$newStatus->value} não é permitida."
                    ], 422);
                }
                $billing->status = $newStatus;
            }

            // 2. Lógica de Pagamento Parcial (mantendo a segurança do Enum)
            if ($request->has('paid_amount')) {
                $billing->paid_amount = $validated['paid_amount'];
                
                if ($billing->paid_amount >= $billing->total_amount) {
                    $billing->status = BillingStatus::PAID;
                } elseif ($billing->paid_amount > 0) {
                    $billing->status = BillingStatus::PARTIAL_PAID;
                }
            }

            $billing->update($validated); // O Laravel cuidará de salvar o valor do Enum no banco

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $billing
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
