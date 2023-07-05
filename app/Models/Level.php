<?php

namespace App\Models;

use App\Enums\StatusLevelOfAuthenticatedEnum;
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

        if($userLevel === $this) {
            return StatusLevelOfAuthenticatedEnum::CURRENT();
        }else if($userLevel < $this) {
            return StatusLevelOfAuthenticatedEnum::PASSED();
        }else{
            return StatusLevelOfAuthenticatedEnum::CURRENT();
        }
    }
}
