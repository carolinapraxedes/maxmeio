<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        try {
            $clients = Client::all();
            return response()->json([
                'status' => 'success',
                'data' => $clients
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível recuperar a lista de clientes.',
                'msg_error' => "Erro ao recuperar lista de clientes: " . $e->getMessage()
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
                'name' => 'required|string|max:255',
                'document' => 'required|string|unique:clients',
                'credit_balance' => 'nullable|numeric|min:0'
            ]);

            $client = Client::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Cliente criado com sucesso!',
                'data' => $client
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
                'message' => "Erro ao criar cliente!",
                'msg_error' => "Erro ao criar cliente: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $client = Client::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cliente não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar cliente.',
                'msg_error' => "Erro ao buscar cliente: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'document' => 'required|string|unique:clients',
                'credit_balance' => 'nullable|numeric|min:0'
            ]);

            $client->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Cliente atualizado com sucesso!',
                'data' => $client
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Cliente não encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar cliente.',
                'msg_error' => "Erro ao atualizar cliente: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Cliente removido com sucesso.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Cliente não encontrado.',
                'msg_error' => "Cliente não encontrado: " . $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Erro ao remover cliente.',
                'msg_error' => "Erro ao remover cliente: " . $e->getMessage()
            ], 500);
        }
    }


}
