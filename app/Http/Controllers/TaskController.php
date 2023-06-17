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

    private function checkUserWorkspaceProjectSprint(Workspace $workspace, Project $project, Sprint $sprint)
    {
        $this->user = Auth::user();
        $this->workspace = $this->user->workspaces()->where('id', $workspace->id)->first();

        if (!$this->workspace) {
            return response()->json(['message' => 'This workspace does not belong to the authenticated user.'], 403);
        }

        $this->project = $this->workspace->projects()->where('id', $project->id)->first();

        if (!$this->project) {
            return response()->json(['message' => 'This project does not belong to the specified workspace.'], 403);
        }

        $this->sprint = $this->project->sprints()->where('id', $sprint->id)->first();

        if (!$this->sprint) {
            return response()->json(['message' => 'This sprint does not belong to the specified project.'], 403);
        }

        return true;
    }

    public function index(Request $request, Workspace $workspace, Project $project, Sprint $sprint)
    {
        $check = $this->checkUserWorkspaceProjectSprint($workspace, $project, $sprint);

        if ($check !== true) {
            return $check; // It will return a response if the check fails.
        }

        $tasks = Task::query()
            ->join('sprints', 'tasks.sprint_id', '=', 'sprints.id')
            ->where('sprints.id', $sprint->id);

        // Apply filters
        $filters = $request->only(['status', 'priority', 'assigned_to']);
        if (!empty($filters['status'])) {
            $tasks->where('tasks.status_id', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $tasks->where('tasks.priority_id', $filters['priority']);
        }
        if (!empty($filters['assigned_to'])) {
            $tasks->where('tasks.assigned_to', $filters['assigned_to']);
        }

        // Apply sorting
        $sortField = $request->input('sort_field');
        $sortOrder = $request->input('sort_order');
        if (!empty($sortField) && !empty($sortOrder)) {
            $tasks->orderBy($sortField, $sortOrder);
        }

        $tasks = $tasks->get();

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request, Workspace $workspace, Project $project, Sprint $sprint)
    {
        $check = $this->checkUserWorkspaceProjectSprint($workspace, $project, $sprint);

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
