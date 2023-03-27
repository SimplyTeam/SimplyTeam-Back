<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'estimated_timestamp',
        'realized_timestamp',
        'deadline',
        'is_finish',
        'sprint_id',
        'priority_id',
        'status_id'
    ];

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_tasks');
    }

    public function previousTask()
    {
        return $this->belongsTo(Task::class, 'id', 'previous_task_id');
    }

    public function nextTask()
    {
        return $this->hasOne(Task::class, 'previous_task_id', 'id');
    }
}
