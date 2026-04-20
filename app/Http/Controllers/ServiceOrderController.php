<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ServiceOrderController extends Controller
{
    public function index()
    {
        try {
            $serviceOrders = ServiceOrder::with(['contract.client', 'user','statusHistory'])->get();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Listagem de ordens de serviço recuperada.',
                'data'   => $serviceOrders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao listar ordens de serviço.',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id'     => 'required|exists:contracts,id',
            'user_id'         => 'required|exists:users,id',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'estimated_hours' => 'required|numeric|min:0',
            'status'          => [Rule::enum(ServiceOrderStatus::class)],
        ]);

        try {
            $serviceOrder = ServiceOrder::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Ordem de serviço criada com sucesso.',
                'data'    => $serviceOrder->load(['contract', 'user'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Não foi possível criar uma Ordem de serviço!',
                'msg_error' => $e->getMessage()

            ], 500);
        }
    }

    public function show(ServiceOrder $serviceOrder)
    {
        try {
            // O $serviceOrder já chega aqui instanciado pelo Laravel
            // Usamos o load para trazer o histórico e as relações
            $data = $serviceOrder->load(['contract.client', 'user', 'statusHistory.user']);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Detalhes da ordem de serviço recuperados.',
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'    => 'error',
                'code'      => 500,
                'message'   => 'Erro ao recuperar os detalhes da ordem de serviço.',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $validated = $request->validate([
            'user_id'         => 'sometimes|exists:users,id',
            'title'           => 'sometimes|string|max:255',
            'estimated_hours' => 'sometimes|numeric|min:0',
            'actual_hours'    => 'sometimes|numeric|min:0',
            'status'          => ['sometimes', Rule::enum(ServiceOrderStatus::class)],
        ]);

        try {
            DB::transaction(function () use ($serviceOrder, $validated) {
                // Se o status mudou, a auditoria deve ser disparada. 
                // Dica: Se você usar Observers, não precisa de código extra aqui.
                $serviceOrder->update($validated);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Ordem de serviço atualizada.',
                'data'    => $serviceOrder->fresh(['user'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Erro ao atualizar a ordem de serviço',
                'msg_error' => $e->getMessage()
            
            ], 500);
        }
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        try {
            $serviceOrder->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Ordem de serviço removida com sucesso.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Não foi possível remover a ordem de serviço!',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }


}
