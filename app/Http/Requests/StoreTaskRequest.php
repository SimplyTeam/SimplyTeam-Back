<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreTaskRequest",
 *     type="object",
 *     title="Store Task Request",
 *     description="Request body for storing a task",
 *     required={
 *         "label", "estimated_timestamp", "realized_timestamp",
 *         "deadline", "is_finish", "priority_id", "status_id"
 *     },
 *     @OA\Property(
 *         property="label",
 *         type="string",
 *         description="The label of the task",
 *         maxLength=255,
 *         example="Implement API endpoint"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         maxLength=500,
 *         description="Description of the task",
 *         example="Endpoint for retrieving user data"
 *     ),
 *     @OA\Property(
 *         property="estimated_timestamp",
 *         type="integer",
 *         description="Estimated time for task completion",
 *         example=120
 *     ),
 *     @OA\Property(
 *         property="realized_timestamp",
 *         type="integer",
 *         description="Actual time taken for task completion",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Deadline for the task",
 *         example="2023-08-20"
 *     ),
 *     @OA\Property(
 *         property="is_finish",
 *         type="boolean",
 *         description="Flag indicating if the task is finished",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="priority_id",
 *         type="integer",
 *         description="ID of the task's priority",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="status_id",
 *         type="integer",
 *         description="ID of the task's status",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="sprint_id",
 *         type="integer",
 *         description="ID of the sprint the task belongs to",
 *         example=3
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="ID of the parent task (if any)",
 *         example=4
 *     ),
 *     @OA\Property(
 *         property="assigned_to",
 *         type="array",
 *         description="Array of user IDs assigned to the task",
 *         @OA\Items(type="integer", example=5)
 *     )
 * )
 */
class StoreTaskRequest extends FormRequest
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
            'label' => 'required|max:255',
            'description' => 'string|nullable|max:500',
            'estimated_timestamp' => 'required|integer',
            'realized_timestamp' => 'required|integer',
            'deadline' => 'required|date',
            'is_finish' => 'required|boolean',
            'priority_id' => 'required|integer|exists:priority,id',
            'status_id' => 'required|integer|exists:status,id',
            'sprint_id' => 'integer',
            'parent_id' => 'integer|nullable|exists:tasks,id',
            'assigned_to' => 'array',
        ];
    }
}
