<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'begin_date',
        'end_date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
