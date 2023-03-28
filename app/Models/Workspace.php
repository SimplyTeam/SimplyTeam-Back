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

    protected static function boot()
    {
        parent::boot();

        self::factory(new WorkspaceFactory());
    }
}
