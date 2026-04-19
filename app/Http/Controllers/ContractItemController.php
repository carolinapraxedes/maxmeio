<?php

namespace App\Http\Controllers;

use App\Models\ContractItem;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ContractItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'contract_id' => 'required|exists:contracts,id',
                'description' => 'required|string|max:255',
                'quantity'    => 'required|integer|min:1',
                'unit_price'  => 'required|numeric|min:0'
            ]);

            $item = ContractItem::create($validated);

            return response()->json([
                'status' => 'success',
                'code'   => 201,
                'message' => 'Item adicionado com sucesso!',
                'data'   => $item
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Dados inválidos.',
                'errors'  => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao adicionar item.',
                'debug_error' => $e->getMessage()
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
            $item = ContractItem::findOrFail($id);

            $validated = $request->validate([
                'description' => 'sometimes|string|max:255',
                'quantity'    => 'sometimes|integer|min:1',
                'unit_price'  => 'sometimes|numeric|min:0'
            ]);

            $item->update($validated);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Item atualizado com sucesso.',
                'data'   => $item
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 404,
                'message' => 'Item não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao atualizar item.',
                'debug_error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = ContractItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Item removido do contrato.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 404,
                'message' => 'Item não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'Erro ao remover item.',
                'debug_error' => $e->getMessage()
            ], 500);
        }
    }
}
