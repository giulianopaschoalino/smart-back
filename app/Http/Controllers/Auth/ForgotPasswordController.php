<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseJson;
use App\Http\Requests\ForgotPasswordRequest;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    use ApiResponse;

    public function __invoke(ForgotPasswordRequest $request)
    {
        $email = $request->validated('email');

        $is_email = User::where('email', $email)->exists();

        if (!$is_email) return ResponseJson::error(
            'Esse e-mail não existe no nosso sistema',
            Response::HTTP_BAD_REQUEST
        );

        DB::table('password_resets')->where('email', $email)->delete();

        $token = random_int(100000, 999999);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);


        Mail::to($email)->send(new ResetPassword($token));

        return ResponseJson::message("Verifique seu e-mail");
    }


    public function verifyPin(Request $request)
    {
    }
}
