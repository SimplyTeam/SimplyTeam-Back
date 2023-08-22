<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="UserQuest",
 *     type="object",
 *     title="UserQuest",
 *     description="UserQuest model",
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The identifier for the user"
 *     ),
 *     @OA\Property(
 *         property="quest_id",
 *         type="integer",
 *         description="The identifier for the quest"
 *     ),
 *     @OA\Property(
 *         property="completed_count",
 *         type="integer",
 *         description="The count of completed quests"
 *     ),
 *     @OA\Property(
 *         property="in_progress",
 *         type="boolean",
 *         description="Indicates if the quest is in progress"
 *     ),
 *     @OA\Property(
 *         property="is_completed",
 *         type="boolean",
 *         description="Indicates if the quest is completed"
 *     ),
 *     @OA\Property(
 *         property="date_completed",
 *         type="string",
 *         format="date-time",
 *         description="The datetime when the quest was completed",
 *         nullable=true
 *     )
 * )
 */
class UserQuest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'users_quests';
    protected $primaryKey = ['user_id', 'quest_id'];
    public $incrementing = false;
    protected $fillable = [
        'user_id',
        'quest_id',
        'completed_count',
        'in_progress',
        'is_completed',
        'date_completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
