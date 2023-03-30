<?php

namespace App\Models;

use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'link_between_users_and_workspaces', 'workspace_id', 'user_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::factory(new WorkspaceFactory());
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
