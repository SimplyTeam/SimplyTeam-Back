<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSprintRequest;
use App\Http\Requests\UpdateSprintRequest;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SprintController extends Controller
{
    // Use constructor to check authenticated user's access to a project
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $workspace = $request->route('workspace');
            $project = $request->route('project');
            $user = $request->user();

            if (!$project || !$workspace->users->contains($user) || !$workspace->projects->contains($project)) {
                return response()->json('Unauthorized', 401);
            }

            return $next($request);
        });
    }

    public function index(Request $request, Workspace $workspace, Project $project)
    {
        // Get the list of sprints, filter by 'is_closed' if required
        $sprints = $project->sprints()
            ->when($request->closing_date, function ($query) {
                return $query->whereDate('closing_date', '!=', null);
            })
            ->orderBy('begin_date', 'desc')
            ->get();

        return response()->json($sprints, 200);
    }

    public function store(StoreSprintRequest $request, Workspace $workspace, Project $project)
    {
        $validatedata = $request->validated();

        $sprint = new Sprint($validatedata);
        $project->sprints()->save($sprint);

        return response()->json($sprint, 201);
    }

    public function update(UpdateSprintRequest $request, Workspace $workspace, Project $project, Sprint $sprint)
    {
        if(!$project->sprints->contains($sprint)) {
            return response()->json('Unauthorized', 401);
        }

        // Validate and get validated data
        $data = $request->validated();

        // Update the sprint
        $sprint->update($data);

        return response()->json($sprint, 200);
    }

    public function remove(Workspace $workspace, Project $project, Sprint $sprint)
    {
        if(!$project->sprints->contains($sprint)) {
            return response()->json('Unauthorized', 401);
        }

        $sprint->delete();

        return response()->json('Sprint deleted', 200);
    }
}

