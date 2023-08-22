<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="GetQuestControllerRequest",
 *     type="object",
 *     title="Get Quests Request",
 *     description="Request parameters for fetching quests",
 *     @OA\Property(
 *         property="quest_type",
 *         type="integer",
 *         description="The type of quest to fetch",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="filter_by",
 *         type="string",
 *         description="Filter quests by a specific attribute",
 *         example="category"
 *     ),
 *     @OA\Property(
 *         property="in_progress_only",
 *         type="boolean",
 *         description="Flag to fetch only the quests in progress",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="default_order",
 *         type="string",
 *         description="Ordering of quests (ascending or descending)",
 *         example="asc",
 *         enum={"asc", "desc"}
 *     )
 * )
 */
class GetQuestControllerRequest extends FormRequest
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
            'quest_type' => 'nullable|integer|exists:quest_types,id',
            'filter_by' => 'nullable',
            'in_progress_only' => 'nullable|boolean',
            'default_order' => 'nullable|in:asc,desc'
        ];
    }
}
