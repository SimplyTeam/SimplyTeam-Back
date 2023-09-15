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
            'status' => $this->status_current_authenticated_user
        ];
    }
}
