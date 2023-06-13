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
            'description' => 'required',
            'estimated_timestamp' => 'required|integer',
            'realized_timestamp' => 'required|integer',
            'deadline' => 'required|date',
            'is_finish' => 'required|boolean',
            'workspace_id' => 'required|integer',
            'project_id' => 'required|integer',
            'sprint_id' => 'required|integer',
            'priority_id' => 'required|integer',
            'status_id' => 'required|integer'
        ];
    }
}
