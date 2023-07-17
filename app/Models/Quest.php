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
        'quest_types_id',
    ];

    public function questType()
    {
        return $this->belongsTo(QuestType::class);
    }
}
