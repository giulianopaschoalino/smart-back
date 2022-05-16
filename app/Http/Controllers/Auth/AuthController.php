<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginResquest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginResquest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials))
        {
            abort(401, 'Inavalid Credentials');
        }

        $user = auth()->user();
        $token = $user->createToken('API Token');

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user
        ], 200);

    }

    public function login2(LoginResquest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials))
        {
            abort(401, 'Inavalid Credentials');
        }

        $request->session()->regenerate();

        return response()->json([], 200);

    }

    public function logout(Request $request): JsonResponse
    {
        $requestToken = $request->header('authorization');

        $token = (new PersonalAccessToken())
            ->findToken(str_replace('Bearer ','', $requestToken));

       $token->delete();

       return response()->json([
           'message' => 'Roken Revoked.'
       ], 200);
    }
}
