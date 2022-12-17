<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        $username_rule = $this->user_type == 'intern' ? 'interns,student_number' : 'users,username';
        
        return [
            'username'      => 'required|exists:' . $username_rule,
            'password'      => 'required',
        ];
    }

    public function messages()
    {
        $username_exist_message =  $this->user_type == 'intern' ? 
            'Student does not exist.' : 
            'User does not exist.';
        
        return [
            'username.required'     => 'Please input your username',
            'username.exists'       => $username_exist_message,
            'password.required'     => 'Please input your password',
        ];
    }
}
