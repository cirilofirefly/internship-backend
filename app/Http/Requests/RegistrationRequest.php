<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationRequest extends FormRequest
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
        return [
            'first_name' => 'required|alpha|min:2|max:20',
            'last_name'  => 'required|alpha|min:2|max:20',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|max:100',
            'user_type'  => ['required', Rule::in(User::USER_TYPES)]
        ];
    }

    public function messages()
    {
        return [
            // 'user_type.in' => 'Invalid value for user type.',
        ];
    }
}
