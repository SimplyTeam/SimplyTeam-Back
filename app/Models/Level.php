<?php

namespace App\Models;

use App\Enums\UserLevelOfAuthenticatedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Level",
 *     type="object",
 *     title="Level",
 *     description="Level model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the level",
 *     ),
 *     @OA\Property(
 *         property="max_point",
 *         type="integer",
 *         description="The maximum points for the level",
 *     ),
 *     @OA\Property(
 *         property="min_point",
 *         type="integer",
 *         description="The minimum points for the level",
 *     ),
 *     @OA\Property(
 *         property="statusLevelOfAuthenticatedUser",
 *         type="string",
 *         description="The status of the level for the authenticated user",
 *         enum={"current", "future", "passed"},
 *     ),
 * )
 */
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
        if (!$user) return null;

        $userLevel = $user->level;

        if ($userLevel->id === $this->id) {
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::CURRENT->value;
        } elseif ($userLevel->id < $this->id) {
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::FUTURE->value;
        } else {
            $userLevelToReturn = UserLevelOfAuthenticatedEnum::PASSED->value;
        }

        return $userLevelToReturn;
    }
}
