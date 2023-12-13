<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginResquest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginResquest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials))
        {
            abort(401, 'Inavalid Credentials');
        }

        $user = User::with('roles')->where('email', $credentials['email'])->firstOrFail();

        $token = $user->createToken('API Token', $user->roles->pluck('name')->toArray());

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user
        ], 200);

    }

    public function logout(Request $request): JsonResponse
    {
        $requestToken = $request->header('authorization');

        $array = Str::of($requestToken)->explode('|');

        $token = (new PersonalAccessToken())
            ->findToken($array->offsetGet(1));

        if (!$token){
            return response()->json([
                'message' => 'Token has already been revoked.'
            ], 500);
        }

        $token->delete();

        return response()->json([
            'message' => 'Token Revoked.'
        ], 200);
    }
}
