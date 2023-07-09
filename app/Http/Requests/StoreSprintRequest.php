<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this if needed
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'begin_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:begin_date'
        ];
    }
}
