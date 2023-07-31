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
}
