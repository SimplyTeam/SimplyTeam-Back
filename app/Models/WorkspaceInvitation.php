<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="WorkspaceInvitation",
 *     description="Workspace Invitation model",
 *     title="WorkspaceInvitation",
 *     required={"email", "workspace_id", "token"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the Workspace Invitation",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address to which the invitation was sent",
 *         example="user@example.com"
 *     ),
 *     @OA\Property(
 *         property="workspace_id",
 *         type="integer",
 *         description="ID of the associated Workspace",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Invitation token for verification",
 *         example="abcd1234"
 *     ),
 *     @OA\Property(
 *         property="accepted_at",
 *         type="string",
 *         format="date-time",
 *         description="Time when the invitation was accepted"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Time when the invitation was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last time the invitation was updated"
 *     ),
 *     @OA\Property(
 *         property="invited_by_id",
 *         type="integer",
 *         description="ID of the user who sent the invitation",
 *         example=3
 *     )
 * )
 */
class WorkspaceInvitation extends Model
{
    use HasFactory;

    protected $table = "workspaces_invitations";

    protected $fillable = [
        'email',
        'workspace_id',
        'token',
        'accepted_at',
        'created_by_id'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }
}
