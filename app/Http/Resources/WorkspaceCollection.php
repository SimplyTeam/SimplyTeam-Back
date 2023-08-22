<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="WorkspaceCollection",
 *     description="Collection of Workspaces",
 *     title="WorkspaceCollection",
 *     @OA\Property(
 *         property="workspaces",
 *         type="array",
 *         description="Array of workspaces",
 *         @OA\Items(ref="#/components/schemas/WorkspaceResource")
 *     )
 * )
 */
class WorkspaceCollection extends ResourceCollection
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
                'workspaces' => $this->collection
        ];
    }
}
