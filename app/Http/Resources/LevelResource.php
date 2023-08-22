<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LevelResource",
 *     description="Level Resource representation",
 *     title="LevelResource",
 *     required={"id", "max_point", "min_point", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the Level",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="max_point",
 *         type="integer",
 *         description="Maximum points for the Level",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="min_point",
 *         type="integer",
 *         description="Minimum points required for the Level",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status of the Level for the currently authenticated user",
 *         example="active"
 *     )
 * )
 */
class LevelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'max_point' => $this->max_point,
            'min_point' => $this->min_point,
            'status' => $this->status_current_authenticated_user
        ];
    }
}
