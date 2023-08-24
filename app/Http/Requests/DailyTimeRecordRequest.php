<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyTimeRecordRequest extends FormRequest
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
        return[
            'date'             => 'required',
            'am_start_time'    => 'required|date_format:H:i',
            'am_end_time'      => 'required|date_format:H:i|after:am_start_time',
            'pm_start_time'    => 'required|date_format:H:i',
            'pm_end_time'      => 'required|date_format:H:i|after:pm_start_time',
        ];
    }
}
