<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precedes extends Model
{
    use HasFactory;

    protected $table = 'precedes';
    public $incrementing = false;
    protected $primaryKey = ['previous_task_id', 'next_task_id'];
    protected $fillable = ['previous_task_id', 'next_task_id'];

}
