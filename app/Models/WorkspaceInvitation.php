<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceInvitation extends Model
{
    use HasFactory;

    protected $table = "workspaces_invitations";

    protected $fillable = [
        'email',
        'workspace_id',
        'token',
        'accepted_at'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
