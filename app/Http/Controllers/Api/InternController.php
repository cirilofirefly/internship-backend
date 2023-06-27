<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InternController extends Controller
{
    public function setInternUserIdAsCookie(Request $request)
    {
        $response = new Response('rfid-registration');
        $response->withCookie(cookie('data', json_encode(['mode' => 'registration', 'user' => $request->id]), 1));
        $user = User::find($request->id);
        return view('scan.register-intern', ['test' => $user]);
    }

    public function getInternCookie(Request $request)
    {
        return $request->cookie('data');
    }
}
