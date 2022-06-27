<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ForgotPasswordRequest;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    use ApiResponse;

    public function __invoke(ForgotPasswordRequest $request)
    {
        $verify = User::where('email', $request->all()['email'])->exists();

        if ($verify) {

            $verify2 = DB::table('password_resets')->where([
                ['email', $request->all()['email']]
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }

            $token = random_int(100000, 999999);
            $password_reset = DB::table('password_resets')->insert([
                'email' => $request->all()['email'],
                'token' => $token,
                'created_at' => Carbon::now()

            ]);

            if ($password_reset) {
                $sendMail = Mail::to($request->all()['email'])->send(new ResetPassword($token));

                return $this->successResponse(true, "Please check your email for a 6 digit pin", 200);
            }
        } else {
            return $this->errorResponse(false, "This email does not exist", 400);
        }
    }


    public function verifyPin(Request $request)
    {


    }
}
