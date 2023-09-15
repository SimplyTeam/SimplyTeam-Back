<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'users' => $this->users->map(function ($user) {
                $link = $user->linksBetweenUsersAndWorkspaces->firstWhere('workspace_id', $this->id);
                $is_PO = $link ? $link->is_PO : false;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_PO' => $is_PO
                ];
            }),
            'projects' => ProjectResource::collection($this->projects)
        ];
    }
}
