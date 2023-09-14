<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetQuestControllerRequest extends FormRequest
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
            'quest_type' => 'nullable|integer|exists:quest_types,id',
            'filter_by' => 'nullable',
            'in_progress_only' => 'nullable|boolean',
            'default_order' => 'nullable|in:asc,desc'
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
            'quest_type.integer' => 'Le champ quest_type doit être un entier.',
            'quest_type.exists' => 'Le type de quête sélectionné est invalide.',
            'in_progress_only.boolean' => 'Le champ in_progress_only doit être vrai ou faux.',
            'default_order.in' => 'Le champ default_order doit être "asc" ou "desc".'
        ];
    }
}
