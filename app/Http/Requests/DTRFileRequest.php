<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DTRFileRequest extends FormRequest
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
            [
                'start_date' => "required|date",
                'end_date'   => "required|date|after:start_date",
                'file'       => "required|mimes:jpg,png,pdf",
            ] :
            [
                'start_date' => "required|date",
                'end_date'   => "required|date|after:start_date",
            ];
    }
}
