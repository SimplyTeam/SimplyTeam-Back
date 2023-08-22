<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UserLoginRequest",
 *     type="object",
 *     title="User Login Request",
 *     description="Request body for user login",
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="The email of the user",
 *         example="user@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="The password of the user",
 *         example="password123"
 *     )
 * )
 */
class UserLoginRequest extends FormRequest
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
            'email' => 'email|required',
            'password' => 'required',
        ];
    }
}
