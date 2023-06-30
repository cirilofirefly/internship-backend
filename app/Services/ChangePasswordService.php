<?php

namespace App\Http\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class ChangePasswordService
{

    public function changePassword($request)
    {
        try {
            if (!Hash::check($request->old_password, $request->user()->password))
                return [401, 'Old Password does not match'];

            User::whereId($request->user()->id)->update(['password' => $request->new_password]);
            return [200, 'Password changed successfully.'];
        } catch (Exception $exception) {
            return [500, 'Someting wrong. Try again later.'];
        }
    }
}
