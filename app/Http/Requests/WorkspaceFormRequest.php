<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="WorkspaceFormRequest",
 *     type="object",
 *     title="Workspace Form Request",
 *     description="Request body for creating/updating a workspace",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the workspace",
 *         example="MyWorkspace"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the workspace",
 *         example="This is my personal workspace"
 *     ),
 *     @OA\Property(
 *         property="invitations",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             format="email",
 *             example="user@example.com"
 *         ),
 *         description="List of email addresses to send workspace invitations"
 *     )
 * )
 */
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
            'description' => 'nullable|string',
            'invitations' => 'sometimes|array',
            'invitations.*' => 'email'
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            "name.required" => "Le nom doit être renseigné !",
            "name.max" => "Le nom doit avoir une taille max de 128 caractères !",
            "description.string" => "La description doit être un texte",
        ];
    }
}
