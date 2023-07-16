<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $this->revokeExistingTokens(auth()->user());

            $token = auth()->user()->createToken('admin');

            return response()->json([
                "access_token" => $token->plainTextToken,
                "token_type" => "Bearer"
            ]);
        }

        abort(403, 'Invalid credentials.');
    }

    private function revokeExistingTokens(User $user)
    {
        $user->tokens()->delete();
    }

    public function logout(): JsonResponse
    {
        if (Auth::guard('sanctum')->user()) {
            Auth::guard('sanctum')->user()->currentAccessToken()->delete();
    
            return response()->json([
                "status" => true
            ]);
        }
    
        return response()->json([
            "status" => false,
            "message" => "User not authenticated."
        ], 401);
    }
}
