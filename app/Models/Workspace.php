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
        return $this->belongsToMany(User::class, 'link_between_users_and_workspaces', 'id_workspace', 'id_user');
    }

    protected static function boot()
    {
        parent::boot();

        self::factory(new WorkspaceFactory());
    }
}
