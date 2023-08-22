<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="QuestType",
 *     type="object",
 *     title="QuestType",
 *     description="QuestType model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the quest type",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="label",
 *         type="string",
 *         description="The label of the quest type"
 *     )
 * )
 */
class QuestType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['label'];
}
