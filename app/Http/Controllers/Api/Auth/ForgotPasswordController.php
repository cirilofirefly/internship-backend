<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['token'] = Str::random(100);

        // Create a new code
        $resetCodePassword = ResetCodePassword::create($data);
        $resetCodePassword->full_name = User::firstWhere('email', $request->email)->full_name;

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($resetCodePassword));

        return response(['message' => trans('passwords.sent')], 200);
    }
}
