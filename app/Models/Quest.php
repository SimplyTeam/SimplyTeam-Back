<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
