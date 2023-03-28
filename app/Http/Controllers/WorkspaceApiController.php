<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Http\Resources\WorkspaceCollection;
use App\Http\Resources\WorkspaceResource;
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

        $workspace->users()->attach($request->user());

        $workspace->save();

        return (new WorkspaceResource($workspace))->response()->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $workspace = $request->user()->workspaces()->find($id);

        if (!$workspace) {
            return response()->json(['error' => 'You don\'t have access to this workspace'], 403);
        }

        return new WorkspaceResource($workspace);
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
