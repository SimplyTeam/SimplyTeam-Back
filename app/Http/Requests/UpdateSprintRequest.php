<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateSprintRequest",
 *     type="object",
 *     title="Update Sprint Request",
 *     description="Request body for updating a sprint",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the sprint",
 *         example="Updated Sprint 1"
 *     ),
 *     @OA\Property(
 *         property="begin_date",
 *         type="string",
 *         format="date",
 *         description="The updated start date of the sprint",
 *         example="2023-09-01"
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="string",
 *         format="date",
 *         description="The updated end date of the sprint",
 *         example="2023-09-15"
 *     ),
 *     @OA\Property(
 *         property="closing_date",
 *         type="string",
 *         format="date",
 *         description="The closing date of the sprint",
 *         example="2023-09-12"
 *     )
 * )
 */
class UpdateSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this if needed
    }

    public function rules()
    {
        return [
            'name' => 'string',
            'begin_date' => 'date',
            'end_date' => 'date|after_or_equal:begin_date',
            'closing_date' => 'date|after_or_equal:begin_date|before_or_equal:end_date',
        ];
    }
}
