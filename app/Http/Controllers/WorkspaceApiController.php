<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Http\Resources\WorkspaceCollection;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceApiController extends Controller
{
    public function index(): WorkspaceCollection
    {
        $workspaces = Workspace::all();

        return new WorkspaceCollection($workspaces);
    }
}
