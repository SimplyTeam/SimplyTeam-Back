<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GithubRequest extends FormRequest
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
            'title' => 'required|string',
            'body' => 'required|string',
            'labels' => 'required|array'
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
            'title.required' => 'Le champ titre est obligatoire.',
            'title.string' => 'Le champ titre doit être une chaîne de caractères.',

            'body.required' => 'Le champ corps est obligatoire.',
            'body.string' => 'Le champ corps doit être une chaîne de caractères.',

            'labels.required' => 'Le champ étiquettes est obligatoire.',
            'labels.array' => 'Le champ étiquettes doit être un tableau.'
        ];
    }
}
