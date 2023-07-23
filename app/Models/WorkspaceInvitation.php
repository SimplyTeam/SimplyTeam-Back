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
        'is_PO',
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
