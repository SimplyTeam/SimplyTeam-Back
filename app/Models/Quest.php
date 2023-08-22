<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Quest",
 *     type="object",
 *     title="Quest",
 *     description="Quest model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the quest",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the quest"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the quest"
 *     ),
 *     @OA\Property(
 *         property="reward_points",
 *         type="integer",
 *         description="The number of points awarded for completing the quest"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer",
 *         description="The level of the quest"
 *     ),
 *     @OA\Property(
 *         property="quest_types_id",
 *         type="integer",
 *         description="The identifier of the quest type associated with the quest"
 *     ),
 *     @OA\Property(
 *         property="previous_quest_id",
 *         type="integer",
 *         description="The identifier of the previous quest"
 *     ),
 *     @OA\Property(
 *         property="count",
 *         type="integer",
 *         description="The count of quests"
 *     ),
 *     @OA\Property(
 *         property="questType",
 *         ref="#/components/schemas/QuestType",
 *         description="The type of the quest"
 *     ),
 *     @OA\Property(
 *         property="userQuests",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserQuest"),
 *         description="List of user quests associated with the quest"
 *     ),
 * )
 */
class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'reward_points',
        'level',
        'quest_types_id',
        'previous_quest_id',
        'count'
    ];

    public $timestamps = false;

    public function questType()
    {
        return $this->belongsTo(QuestType::class);
    }

    public function userQuests()
    {
        return $this->hasMany(UserQuest::class, 'quest_id');
    }
}
