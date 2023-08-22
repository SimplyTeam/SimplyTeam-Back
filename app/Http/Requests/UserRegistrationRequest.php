<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UserRegistrationRequest",
 *     type="object",
 *     title="User Registration Request",
 *     description="Request body for user registration",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the user",
 *         example="John Doe"
 *     ),
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
 *         description="The password of the user. It must contain at least one uppercase letter, one lowercase letter, one special character, and one digit.",
 *         example="Password@123"
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         description="Password confirmation for validation",
 *         example="Password@123"
 *     )
 * )
 */
class UserRegistrationRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[a-zA-Z\d\W]{8,}$/',
                'confirmed'
            ],
        ];
    }

    public function messages()
    {
        return parent::messages() + [
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one special character, and one digit.'
            ];
    }
}
