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

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Le nom doit être une chaîne de caractères.',

            'begin_date.date' => 'La date de début doit être une date valide.',

            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être ultérieure ou égale à la date de début.',

            'closing_date.date' => 'La date de clôture doit être une date valide.',
            'closing_date.after_or_equal' => 'La date de clôture doit être ultérieure ou égale à la date de début.',
            'closing_date.before_or_equal' => 'La date de clôture doit être antérieure ou égale à la date de fin.'
        ];
    }
}
