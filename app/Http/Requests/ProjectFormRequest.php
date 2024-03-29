<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectFormRequest extends FormRequest
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
            'name' => 'required|max:128'
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            "name.required" => "Le nom doit être renseigné !",
            "name.max" => "Le nom doit avoir au maximum 128 caractères !"
            ];
    }
}
