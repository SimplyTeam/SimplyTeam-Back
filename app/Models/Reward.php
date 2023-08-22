<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Reward",
 *     type="object",
 *     title="Reward",
 *     description="Reward model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the reward",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="coupon",
 *         type="string",
 *         description="The coupon code or reference of the reward"
 *     ),
 *     @OA\Property(
 *         property="date_achieved",
 *         type="string",
 *         format="date",
 *         description="The date when the reward was achieved"
 *     ),
 *     @OA\Property(
 *         property="level_id",
 *         type="integer",
 *         description="The associated level's ID for the reward"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The associated user's ID who achieved the reward"
 *     )
 * )
 */
class Reward extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'coupon',
        'date_achieved',
        'level_id',
        'user_id',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
