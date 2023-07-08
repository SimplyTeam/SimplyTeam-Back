<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Workspace;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
     * Display a listing of the projects for a user in the given workspace.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $levels = Level::where('id', '>=', $user->level->id - 3)
            ->where('id', '<=', $user->level->id + 3)
            ->orderBy('id')
            ->get();

        foreach ($levels as $level) {
            $level->status = $level->getStatusLevelOfAuthenticatedUserAttribute();
        }

        return [
            "levels" => $levels
        ];
    }
}
