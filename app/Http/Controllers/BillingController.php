<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Contract;
use Exception;
use App\Enums\BillingStatus;
use App\Exceptions\TransitionException;
use App\Services\BillingService;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    // *Para fins de teste
    // *public function index()
    // {
    //     try {
            
    //         $billings = Billing::with(['contract.client', 'contract.items'])->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'code'   => 200,
    //             'message' => 'Listagem de cobranças recuperada.',
    //             'data'   => $billings
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'code'   => 500,
    //             'message' => 'Erro ao listar cobranças.',
    //             'msg_error' => $e->getMessage()
    //         ], 500);
    //     }
    // }*/

    public function index(Request $request)
    {
        try {
            // 1. Iniciamos a query com Eager Loading (o seu 'with')
            // Importante: Não usamos ->get() aqui ainda, pois vamos aplicar filtros.
            $query = Billing::with(['contract.client', 'contract.items']);

            // 2. Filtro por múltiplos status (Ex: ?status=pendente,pago)
            if ($request->has('status')) {
                $status = is_array($request->status) ? $request->status : explode(',', $request->status);
                $query->whereIn('status', $status);
            }

            // 3. Filtro por intervalo de datas
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // 4. Busca por nome ou documento do cliente
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('contract.client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
                });
            }

            // 5. Ordenação por campos específicos (Whitelist de segurança)
            $allowedSorts = ['id', 'status', 'created_at', 'partial_paid'];
            $sortField = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'created_at';
            $sortOrder = $request->order === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            // 6. Paginação (Obrigatório pelo enunciado)
            // O paginate() já retorna um objeto com 'data', 'current_page', 'total', etc.
            $perPage = $request->get('per_page', 15);
            $paginatedData = $query->paginate($perPage);
            $statusCode = Response::HTTP_OK; 

            return response()->json([
                'status' => 'success',
                'code'   => $statusCode,
                'message' => 'Listagem de cobranças recuperada.',
                'data'   => $paginatedData 
            ], $statusCode);

        } catch (\Exception $e) {
            $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'status' => 'error',
                'code'   => $errorCode,
                'message' => 'Erro ao listar cobranças.',
                'msg_error' => $e->getMessage()
            ], $errorCode);
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
                'partial_paid'  => 0, 
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
    /**public function update(Request $request, $id)
    {
        try {
            $billing = Billing::findOrFail($id);

            $validated = $request->validate([
                'status' => 'sometimes|string',
                'partial_paid' => 'sometimes|numeric|min:0',
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
            if ($request->has('partial_paid')) {
                $billing->partial_paid = $validated['partial_paid'];
                
                if ($billing->partial_paid >= $billing->total_amount) {
                    $billing->status = BillingStatus::PAID;
                } elseif ($billing->partial_paid > 0) {
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
    }*/

    public function update(Request $request, $id, BillingService $service)
    {
        $billing = Billing::findOrFail($id);
        
        try {
            $updatedBilling = $service->updateStatus($billing, $request->all());
            
            return response()->json([
                'status' => 'success',
                'data' => $updatedBilling
            ]);
            
        } catch (TransitionException $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 422);
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
