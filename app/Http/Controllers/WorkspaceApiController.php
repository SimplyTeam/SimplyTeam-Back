<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Http\Resources\WorkspaceCollection;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $workspaces = $user->workspaces;

        return new WorkspaceCollection($workspaces);
    }

    public function store(WorkspaceFormRequest $request)
    {
        $workspace = Workspace::create($request->validated());

        return response()->json($workspace, 201);
    }

    public function show(Workspace $workspace)
    {
        return response()->json($workspace, 200);
    }

    public function update(WorkspaceFormRequest $request, Workspace $workspace)
    {
        $workspace->update($request->validated());

        return response()->json($workspace, 200);
    }

    public function destroy(Workspace $workspace)
    {
        $workspace->delete();

        return response()->json(null, 204);
    }
}
