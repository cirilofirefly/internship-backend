<?php

namespace App\Http\Requests;

use App\Models\Requirement;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RequirementRequest extends FormRequest
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
            'requirement_type'  => "required|in:" . 
                Requirement::APPLICATION_LETTER . ','  . 
                Requirement::RESUME . ',' . 
                Requirement::COMPANY_PROFILE . ','  . 
                Requirement::LETTER_OF_ENDORSEMENT . ',' . 
                Requirement::MEMORANDUM_OF_AGREEMENT,

            'file'              => "required|mimes:jpg,png,pdf,docx,doc",
        ];
    }
}
