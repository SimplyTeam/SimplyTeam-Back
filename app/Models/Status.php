<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Status",
 *     type="object",
 *     title="Status",
 *     description="Status model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the status",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="label",
 *         type="string",
 *         description="The label of the status"
 *     )
 * )
 */
class Status extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = 'status';

    protected $fillable = [
        'label'
    ];
}
