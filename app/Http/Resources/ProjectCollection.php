<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="ProjectCollection",
 *     description="Collection of Projects",
 *     title="ProjectCollection",
 *     @OA\Property(
 *         property="projects",
 *         type="array",
 *         description="Array of projects",
 *         @OA\Items(ref="#/components/schemas/ProjectResource")
 *     )
 * )
 */
class ProjectCollection extends ResourceCollection
{
    public static $wrap = null;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'projects' => $this->collection
        ];
    }
}
