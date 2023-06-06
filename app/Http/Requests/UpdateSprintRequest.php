<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this if needed
    }

    public function rules()
    {
        return [
            'name' => 'string',
            'begin_date' => 'date',
            'end_date' => 'date|after_or_equal:begin_date',
            'closing_date' => 'date|after_or_equal:begin_date|before_or_equal:end_date',
        ];
    }
}
