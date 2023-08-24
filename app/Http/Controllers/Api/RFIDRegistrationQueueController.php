<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RFIDRegistrationQueueController extends Controller
{
    public function scanRFID($cardId) {
        return response()->json($cardId, 200);
    }

    public function registerRFID($device_token, Request $request)
    {
        return response()->json([$device_token, $request->all()]);
    }

}
