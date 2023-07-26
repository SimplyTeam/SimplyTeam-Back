<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'label' => 'string|max:255',
            'description' => 'string|nullable|max:500',
            'estimated_timestamp' => 'integer',
            'realized_timestamp' => 'integer',
            'deadline' => 'date',
            'is_finish' => 'boolean',
            'priority_id' => 'integer|exists:priority,id',
            'status_id' => 'integer|exists:status,id',
            'assigned_to' => 'array',
            'sprint_id' => 'integer|nullable|exists:sprints,id',
        ];
    }
}
