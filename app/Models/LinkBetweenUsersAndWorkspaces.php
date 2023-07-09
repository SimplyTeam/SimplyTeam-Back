<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkBetweenUsersAndWorkspaces extends Model
{
    use HasFactory;

    protected $table = 'link_between_users_and_workspaces';
    public $timestamps = false;
}
