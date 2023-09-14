<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidatePaymentApiControllerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'premium_expiration_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ]
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
            'premium_expiration_date.required' => 'La date d\'expiration premium est obligatoire.',
            'premium_expiration_date.date' => 'La date d\'expiration premium doit être une date valide.',
            'premium_expiration_date.after_or_equal' => 'La date d\'expiration premium doit être ultérieure ou égale à la date d\'aujourd\'hui.'
        ];
    }
}
