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
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::CURRENT->value;
        }elseif($userLevel->id < $this->id) {
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::FUTURE->value;
        }else{
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::PASSED->value;
        }

        return $userLevelToReturn;
    }
}
