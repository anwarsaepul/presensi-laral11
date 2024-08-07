<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException;
// use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login Success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_id' => $user->company_id,
                    'role' => $user->role,
                    'department' => $user->department,
                    'photo' => $user->photo,
                    'status' => $user->status,
                    'token' => $token,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => "Wrong email or password"
        ], 401);
    }

    public function logout(Request $request)
    {
        $token = request()->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken) {
            $accessToken->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } else {
            return response()->json(['message' => 'Token not found'], 404);
        }
    }
}
