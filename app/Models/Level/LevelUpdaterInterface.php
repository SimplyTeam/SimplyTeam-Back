<?php

namespace App\Models\Level;

use App\Models\User;

protected interface LevelUpdaterInterface
{
    public function updateLevel(): void;
}
