<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordService
{

    public function changePassword($request)
    {
        Log::info($request);
        try {
            if (!Hash::check($request['old_password'], auth()->user()->password))
                return [401, 'Old Password does not match'];

            User::whereId(auth()->user()->id)->update(['password' => Hash::make($request['new_password'])]);
            return [200, 'Password changed successfully.'];
        } catch (Exception $exception) {
            Log::error($exception);
            return [500, 'Someting wrong. Try again later.'];
        }
    }
}
