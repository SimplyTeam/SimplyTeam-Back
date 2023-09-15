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

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le champ nom est obligatoire.',

            'begin_date.required' => 'Le champ date de début doit être renseigné.',
            'begin_date.date' => 'Le champ date de début doit être une date valide.',

            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.date' => 'Le champ date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être ultérieure ou égale à la date de début.'
        ];
    }
}
