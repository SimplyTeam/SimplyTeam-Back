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
            'sprint_id' => 'integer|nullable',
            'parent_id' => 'integer|nullable|exists:tasks,id',
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
            'label.string' => 'Le champ label doit être une chaîne de caractères.',
            'label.max' => 'Le champ label doit contenir au maximum 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description doit contenir au maximum 500 caractères.',

            'estimated_timestamp.integer' => 'Le champ temps estimé doit être un entier.',

            'realized_timestamp.integer' => 'Le champ temps réalisé doit être un entier.',

            'deadline.date' => 'Le champ deadline doit être une date valide.',

            'is_finish.boolean' => 'Le champ "est terminé" doit être vrai ou faux.',

            'priority_id.integer' => 'La priorité doit être un entier.',
            'priority_id.exists' => 'La priorité sélectionnée est invalide.',

            'status_id.integer' => 'Le statut doit être un entier.',
            'status_id.exists' => 'Le statut sélectionné est invalide.',

            'sprint_id.integer' => 'Le champ sprint_id doit être un entier.',

            'parent_id.integer' => 'La tâche parente doit être un entier.',
            'parent_id.exists' => 'La tâche parente sélectionnée est invalide.',

            'assigned_to.array' => 'Le champ "assigné à" doit être un tableau.'
        ];
    }
}
