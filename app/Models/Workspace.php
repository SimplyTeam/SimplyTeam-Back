<?php

namespace App\Models;

use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'link_between_users_and_workspaces', 'workspace_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        self::factory(new WorkspaceFactory());
    }
}
