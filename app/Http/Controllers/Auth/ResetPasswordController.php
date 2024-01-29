<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Helpers\ResponseJsonMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    

    public function __invoke(ResetPasswordRequest $resetPasswordRequest)
    {
        $check = DB::table('password_resets')->where([
            ['email', $resetPasswordRequest->email],
            ['token', $resetPasswordRequest->token],
        ]);

        if ($check->exists()) {

            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);

            if ($difference > 3600) {
                return ResponseJsonMessage::withError("Token Expired", 400);
            }

            DB::table('password_resets')->where([
                ['email', $resetPasswordRequest->email],
                ['token', $resetPasswordRequest->token],
            ])->delete();

            $user = User::with('roles')->firstWhere('email', $resetPasswordRequest->email);

            $user->update([
                'password' => $resetPasswordRequest->password
            ]);

            ResponseJsonMessage::withData($user);

        } else {
            return $this->errorResponse(false, "Invalid token", 401);
        }
    }
}