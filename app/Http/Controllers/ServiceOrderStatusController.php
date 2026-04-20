<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;


class ServiceOrderStatusController extends Controller
{
    public function index(ServiceOrder $serviceOrder)
    {
        try {
            
            $history = $serviceOrder->statusHistory()
                ->with('user:id,name')
                ->orderBy('changed_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => "Histórico da OS #{$serviceOrder->id} recuperado.",
                'data'   => $history
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao recuperar histórico.',
                'msg_error' => $e->getMessage()
            ], 500);
        }
    }
}
