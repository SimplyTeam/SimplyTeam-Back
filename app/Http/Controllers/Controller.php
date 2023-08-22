<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="Simply Team API", version="0.8")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
