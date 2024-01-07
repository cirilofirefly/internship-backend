<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupervisorRequest extends FormRequest
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
        return $this->isMethod('POST') ? 
            $this->postRule() : 
            $this->updateRule();
    }

    private function postRule()
    {
        return [
            'username'           => 'required|unique:users',
            'email'              => 'required|email|unique:users',
            'password'           => 'bail|required|min:8|max:100|confirmed',
            'first_name'         => 'required|min:2|max:20',
            'last_name'          => 'required|min:2|max:20',
            'host_establishment' => 'required',
            'campus_type'        => 'required',
            'designation'        => 'required',
            'coordinator_id'     => 'required',
        ];
    }

    private function updateRule()
    {
        return [
            'username'           => 'required|unique:users,username,' . $this->id,
            'email'              => 'required|email|unique:users,email,' . $this->id,
            'first_name'         => 'required|min:2|max:20',
            'last_name'          => 'required|min:2|max:20',
            'gender'             => 'required',
            'contact_number'     => 'required',
            'birthday'           => 'required',
            'civil_status'       => 'required',
            'nationality'        => 'required',
            'host_establishment' => 'required',
            'campus_type'        => 'required',
            'designation'        => 'required',
        ];
    }


}
