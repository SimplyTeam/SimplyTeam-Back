<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'label' => 'required|max:255',
            'description' => 'string|nullable|max:500',
            'estimated_timestamp' => 'required|integer',
            'realized_timestamp' => 'required|integer',
            'deadline' => 'required|date',
            'is_finish' => 'required|boolean',
            'priority_id' => 'required|integer|exists:priority,id',
            'status_id' => 'required|integer|exists:status,id',
            'sprint_id' => 'integer|nullable|exists:sprints,id',
            'assigned_to' => 'array',
        ];
    }
}
