<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InternRegistration extends FormRequest
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
            'student_number'    => 'required|min:7|max:7',
            'first_name'        => 'required|min:2|max:20',
            'last_name'         => 'required|min:2|max:20',
            'email'             => 'required|email|unique:users',
            'gender'            => 'required',
            'password'          => 'bail|required|min:8|max:100|confirmed',
            'civil_status'      => 'required',
            'contact_number'    => 'required',
            'program'           => 'required',
            'nationality'       => 'required',
            'year_level'        => 'required',
            'section'           => 'required',
            'coordinator_id'    => 'required',
        ];
    }

    public function messages()
    {
        return [
            // 'user_type.in' => 'Invalid value for user type.',
        ];
    }
}
