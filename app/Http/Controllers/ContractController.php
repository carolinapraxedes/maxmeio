<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         try {
            $contract = Contract::all();
            return response()->json([
                'status' => 'success',
                'data' => $contract
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível recuperar a lista de contratos.',
                'msg_error' => "Erro ao recuperar lista de contratos: " . $e->getMessage()
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
                'client_id'      => 'required|exists:clients,id',
                'date_start'     => 'required|date',
                'date_end'       => 'required|date|after_or_equal:date_start',
            ]);

            $contract = Contract::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Contrato criado com sucesso!',
                'data' => $contract
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Erro ao criar contrato!",
                'msg_error' => "Erro ao criar contrato: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $contract
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contrato não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar Contrato.',
                'msg_error' => "Erro ao buscar Contrato: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $contract = Contract::findOrFail($id);

            $validated = $request->validate([
                'client_id'      => 'required|exists:clients,id',
                'date_start'     => 'required|date',
                'date_end'       => 'required|date|after_or_equal:date_start',
            ]);

            $contract->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Cliente atualizado com sucesso!',
                'data' => $contract
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Contrato não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar contrato.',
                'msg_error' => "Erro ao atualizar contrato: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Contrato removido com sucesso.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Contrato não encontrado.',
                'msg_error' => "Contrato não encontrado: " . $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Erro ao remover contrato.',
                'msg_error' => "Erro ao remover contrato: " . $e->getMessage()
            ], 500);
        }
    }
}
