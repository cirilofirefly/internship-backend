<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function saveUser(UserRequest $request)
    {
        return User::updateOrCreate(
            [
                'id' => $request->user_id
            ], 
            $request->except(
                'user_id', 
                'password_confirmation',
                'edit_mode'
            )
        );        
    }

    public function getUsers()
    {
        return response()->json([
            'users' => User::whereNot('user_type', User::SUPER_ADMIN)->paginate(5)
        ]);
    }


    public function getUser($id)
    {
        return User::where('id', $id)->firstOrFail();
    }
}
