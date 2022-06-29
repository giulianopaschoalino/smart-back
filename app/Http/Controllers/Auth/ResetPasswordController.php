<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ApiResponse;

    public function __invoke(ResetPasswordRequest $resetPasswordRequest)
    {
        $check = DB::table('password_resets')->where([
            ['email', $resetPasswordRequest->email],
            ['token', $resetPasswordRequest->token],
        ]);

        if ($check->exists()) {

            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                return $this->errorResponse(false, "Token Expired", 400);
            }

            $delete = DB::table('password_resets')->where([
                ['email', $resetPasswordRequest->email],
                ['token', $resetPasswordRequest->token],
            ])->delete();

            $user = User::with('roles')->firstWhere('email', $resetPasswordRequest->email);

            $user->update([
                'password' => Hash::make($resetPasswordRequest->password)
            ]);

            return $this->successResponse([
                'user' => $user
            ],
                "You can now reset your password",
                200);

        } else {
            return $this->errorResponse(false, "Invalid token", 401);
        }
    }
}