<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    use HasFactory;

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
