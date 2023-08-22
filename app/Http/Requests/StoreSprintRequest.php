<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreSprintRequest",
 *     type="object",
 *     title="Store Sprint Request",
 *     description="Request body for storing a sprint",
 *     required={"name", "begin_date", "end_date"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the sprint",
 *         example="Sprint 1"
 *     ),
 *     @OA\Property(
 *         property="begin_date",
 *         type="string",
 *         format="date",
 *         description="The start date of the sprint",
 *         example="2023-08-01"
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="string",
 *         format="date",
 *         description="The end date of the sprint",
 *         example="2023-08-15"
 *     )
 * )
 */
class StoreSprintRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this if needed
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'begin_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:begin_date'
        ];
    }
}
