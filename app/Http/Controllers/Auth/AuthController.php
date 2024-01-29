<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Helpers\ResponseJsonMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginResquest;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginResquest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            abort(401, 'Inavalid Credentials');
        }

        $user = User::with('roles')->where('email', $credentials['email'])->firstOrFail();

        $token = $user->createToken(
            'API Token',
            $user->roles->pluck('name')->toArray()
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        /**
         * @var \App\Models\User|null;
         */
        $user = Auth::user();

        $user?->currentAccessToken()?->delete();

        return ResponseJsonMessage::withMessage('Token Revoked.');
    }
}
