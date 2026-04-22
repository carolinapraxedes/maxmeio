<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function login(Request $request)
    
    {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        
        $user = User::where('email', $request->email)->first();

        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciais inválidas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login efetuado com sucesso.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Revoga o token atual (Logout).
     */
    public function logout(Request $request)
    {
        try {
            // Elimina o token que está a ser usado na requisição atual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout efetuado com sucesso (token revogado).'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar logout.',
                'msg_error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
