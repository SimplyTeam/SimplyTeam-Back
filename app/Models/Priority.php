<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Priority",
 *     type="object",
 *     title="Priority",
 *     description="Priority model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the priority",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="label",
 *         type="string",
 *         description="The label for the priority",
 *     ),
 * )
 */
class Priority extends Model
{
    use HasFactory;

    protected $table = 'priority';

    protected $fillable = [
        'label'
    ];
}
