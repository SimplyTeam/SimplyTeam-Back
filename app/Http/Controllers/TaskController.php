<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private $user;
    private $workspace;
    private $project;
    private $sprint;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function checkUserWorkspaceProjectSprint($workspaceId, $projectId, $sprintId)
    {
        $this->user = Auth::user();
        $this->workspace = $this->user->workspaces()->where('id', $workspaceId)->first();

        if (!$this->workspace) {
            return response()->json(['message' => 'This workspace does not belong to the authenticated user.'], 403);
        }

        $this->project = $this->workspace->projects()->where('id', $projectId)->first();

        if (!$this->project) {
            return response()->json(['message' => 'This project does not belong to the specified workspace.'], 403);
        }

        $this->sprint = $this->project->sprints()->where('id', $sprintId)->first();

        if (!$this->sprint) {
            return response()->json(['message' => 'This sprint does not belong to the specified project.'], 403);
        }

        return true;
    }

    public function store(StoreTaskRequest $request, Workspace $workspace, Project $project, Sprint $sprint)
    {
        $check = $this->checkUserWorkspaceProjectSprint($workspace->id, $project->id, $sprint->id);

        if ($check !== true) {
            return $check; // It will return a response if the check fails.
        }

        $validatedata = $request->validated();

        $task = new Task();
        $task->label = $validatedata["label"];
        $task->description = $validatedata["description"];
        $task->estimated_timestamp = $validatedata["estimated_timestamp"];
        $task->realized_timestamp = $validatedata["realized_timestamp"];
        $task->deadline = $validatedata["deadline"];
        $task->is_finish = $validatedata["is_finish"];
        $task->priority_id = $validatedata["priority_id"];
        $task->status_id = $validatedata["status_id"];
        $sprint->tasks()->save($task);

        return response()->json(['message' => 'Task created successfully!', 'task' => $task], 201);
    }
}
