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
            'title.required' => 'Le champ titre doit être renseigné !',
            'title.string' => 'Le champ titre doit être une chaîne de caractères.',

            'body.required' => 'Le champ corps doit être renseigné !',
            'body.string' => 'Le champ corps doit être une chaîne de caractères.',

            'labels.required' => 'Des étiquettes doivent être renseignées !',
            'labels.array' => 'Le champ étiquettes doit être un tableau.'
        ];
    }
}
