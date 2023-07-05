<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'max_point' => $this->max_point,
            'min_point' => $this->min_point,
            'is_current_level' => $this->is_current_level,
            'is_passed_level' => $this->is_passed_level,
            'is_next_level' => $this->is_next_level
        ];
    }
}
