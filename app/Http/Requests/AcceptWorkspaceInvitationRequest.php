<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AcceptWorkspaceInvitationRequest",
 *     type="object",
 *     title="Accept Workspace Invitation Request",
 *     description="Request body for accepting a workspace invitation",
 *     required={"token"},
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Token associated with the workspace invitation",
 *         example="a1b2c3d4e5f6a7b8c9d0"
 *     )
 * )
 */
class AcceptWorkspaceInvitationRequest extends FormRequest
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
            'token' => 'required|string',
        ];
    }
}
