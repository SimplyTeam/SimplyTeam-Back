<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
