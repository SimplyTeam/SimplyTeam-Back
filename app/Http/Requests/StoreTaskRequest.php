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
            'sprint_id' => 'integer',
            'parent_id' => 'integer|nullable|exists:tasks,id',
            'assigned_to' => 'array',
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
            'label.required' => 'Le champ label est obligatoire.',
            'label.max' => 'Le champ label doit contenir au maximum 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description doit contenir au maximum 500 caractères.',

            'estimated_timestamp.required' => 'Le champ timestamp estimé est obligatoire.',
            'estimated_timestamp.integer' => 'Le champ timestamp estimé doit être un entier.',

            'realized_timestamp.required' => 'Le champ timestamp réalisé est obligatoire.',
            'realized_timestamp.integer' => 'Le champ timestamp réalisé doit être un entier.',

            'deadline.required' => 'Le champ date limite est obligatoire.',
            'deadline.date' => 'Le champ date limite doit être une date valide.',

            'is_finish.required' => 'Le champ "est terminé" est obligatoire.',
            'is_finish.boolean' => 'Le champ "est terminé" doit être vrai ou faux.',

            'priority_id.required' => 'La priorité est obligatoire.',
            'priority_id.integer' => 'La priorité doit être un entier.',
            'priority_id.exists' => 'La priorité sélectionnée est invalide.',

            'status_id.required' => 'Le statut est obligatoire.',
            'status_id.integer' => 'Le statut doit être un entier.',
            'status_id.exists' => 'Le statut sélectionné est invalide.',

            'sprint_id.integer' => 'Le sprint doit être un entier.',

            'parent_id.integer' => 'La tâche parent doit être un entier.',
            'parent_id.exists' => 'La tâche parent sélectionnée est invalide.',

            'assigned_to.array' => 'Le champ "assigné à" doit être un tableau.'
        ];
    }
}
