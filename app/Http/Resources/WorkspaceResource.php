<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="WorkspaceResource",
 *     description="Workspace Resource representation",
 *     title="WorkspaceResource",
 *     required={"id", "name", "description", "created_at", "updated_at"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the Workspace",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the Workspace",
 *         example="My Workspace"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the Workspace",
 *         example="A brief description about the workspace."
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Time when the Workspace was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last time the Workspace was updated"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         ref="#/components/schemas/UserResource",
 *         description="User who created the Workspace"
 *     ),
 *     @OA\Property(
 *         property="users",
 *         type="array",
 *         description="List of Users associated with the Workspace",
 *         @OA\Items(ref="#/components/schemas/UserResource")
 *     ),
 *     @OA\Property(
 *         property="projects",
 *         type="array",
 *         description="List of Projects under the Workspace",
 *         @OA\Items(ref="#/components/schemas/ProjectResource")
 *     )
 * )
 */
class WorkspaceResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => new UserResource(User::find($this->created_by_id) ?? null),
            'users' => UserResource::collection($this->users),
            'projects' => ProjectResource::collection($this->projects)
        ];
    }
}
