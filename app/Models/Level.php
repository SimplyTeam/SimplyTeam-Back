<?php

namespace App\Models;

use App\Enums\UserLevelOfAuthenticatedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Level extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'max_point',
        'min_point',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getStatusLevelOfAuthenticatedUserAttribute()
    {
        $user = User::getAuthenticatedUser();
        if(!$user) return null;

        $userLevel = $user->level;

        if($userLevel->id === $this->id) {
            return UserLevelOfAuthenticatedEnum::CURRENT->value;
        }else if($userLevel->id < $this->id) {
            return UserLevelOfAuthenticatedEnum::FUTURE->value;
        }else{
            return UserLevelOfAuthenticatedEnum::PASSED->value;
        }
    }
}
