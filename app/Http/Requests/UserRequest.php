<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = $this->user_id != -1 ? [
            'contact_number'    => "required",
            'email'             => "required|email|unique:users,email," . $this->user_id,
            'first_name'        => "required",
            'last_name'         => "required",
            'user_type'         => "required|in:" . User::COORDINATOR . ','  . User::SUPERVISOR . ',' . User::INTERN,
            'username'          => "required|unique:users,username," . $this->user_id,
        ] : [
            'contact_number'    => "required",
            'email'             => "required|email|unique:users,email",
            'first_name'        => "required",
            'last_name'         => "required",
            'password'          => "required|confirmed",
            'user_type'         => "required|in:" . User::COORDINATOR . ','  . User::SUPERVISOR . ',' . User::INTERN,
            'username'          => "required|unique:users,username",
        ];

        return $rules;
    }
}
