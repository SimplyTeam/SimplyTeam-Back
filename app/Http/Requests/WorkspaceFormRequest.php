<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:128',
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            "name.required" => [
                "Le nom doit être renseigné !"
            ],
            "name.max" => [
                "Le nom doit avoir une taille max de 128 caractères !"
            ],
        ];
    }
}
