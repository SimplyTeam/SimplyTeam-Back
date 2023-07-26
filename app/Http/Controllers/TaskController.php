<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    private JsonResponse $missingWorkspaceInUserError;
    private JsonResponse $missingProjectInWorkspaceError;
    private JsonResponse $sprintMissingInProjectError;
    private JsonResponse $missingTaskInProjectError;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->missingWorkspaceInUserError = response()
            ->json(['message' => 'This workspace does not belong to the authenticated user.'], 403);
        $this->missingProjectInWorkspaceError = response()
            ->json(['message' => 'This project does not belong to the specified workspace.'], 403);
        $this->sprintMissingInProjectError = response()
            ->json(['message' => 'This sprint does not belong to the specified project.'], 403);
        $this->missingTaskInProjectError = response()
            ->json(['message' => 'This task does not belong to the specified project.'], 403);
    }

    public function index(Request $request, Workspace $workspace, Project $project, Sprint $sprint)
    {
        $user = $request->user();

        $responseError = null;

        if (!$user->hasWorkspace($workspace)) {
            $responseError = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $responseError = $this->missingProjectInWorkspaceError;
        } elseif (!$project->hasSprint($sprint)) {
            $responseError = $this->sprintMissingInProjectError;
        }

        if ($responseError) {
            return $responseError;
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
            $email = $filters['assigned_to'];
            $tasks = Task::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            });
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

    public function store(StoreTaskRequest $request, Workspace $workspace, Project $project)
    {
        $user = $request->user();

        $responseError = null;

        if (!$user->hasWorkspace($workspace)) {
            $responseError = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $responseError = $this->missingProjectInWorkspaceError;
        }

        if ($responseError) {
            return $responseError;
        }

        $validatedData = $request->validated();

        $task = new Task();

        if (isset($validatedData["sprint_id"])) {
            if ($validatedData['sprint_id'] == null) {
                $task->sprint_id = null;
            } else if ($project->hasSprintWithId($validatedData['sprint_id'])) {
                $task->sprint_id = $validatedData["sprint_id"];
            } else {
                return response()->json(['message' => 'Sprint does not belong to the specified project.'], 403);
            }
        }

        $task->label = $validatedData["label"];
        $task->description = $validatedData["description"];
        $task->estimated_timestamp = $validatedData["estimated_timestamp"];
        $task->realized_timestamp = $validatedData["realized_timestamp"];
        $task->deadline = $validatedData["deadline"];
        $task->is_finish = $validatedData["is_finish"];
        $task->priority_id = $validatedData["priority_id"];
        $task->status_id = $validatedData["status_id"];
        $task->project_id = $project->id;
        $task->created_by = $user->id;
        $task->save();

        if (isset($validatedData["assigned_to"])) {
            $usersToAssign = User::whereIn('email', $validatedData["assigned_to"])->get();
            $task->users()->attach($usersToAssign->pluck('id'));
        }

        return response()->json(['message' => 'Task created successfully!', 'task' => $task], 201);
    }

    public function update(UpdateTaskRequest $request, Workspace $workspace, Project $project, Task $task)
    {
        $user = $request->user();

        $responseError = null;

        if (!$user->hasWorkspace($workspace)) {
            $responseError = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $responseError = $this->missingProjectInWorkspaceError;
        } elseif (!$project->hasTask($task)) {
            $responseError = $this->missingTaskInProjectError;
        }

        if ($responseError) {
            return $responseError;
        }

        $validatedData = $request->validated();
        if (in_array('is_finish', $validatedData)) {
            $validatedData['finished_at'] = $validatedData['is_finish'] ? date('Y-m-d H:i:s') : null;
        }

        if (isset($validatedData["assigned_to"])) {
            $usersToAssign = User::whereIn('email', $validatedData["assigned_to"])->get();

            if (empty($usersToAssign)) {
                $task->users()->detach();
            } else {
                $task->users()->sync($usersToAssign->pluck('id'));
            }
        }

        if (isset($validatedData["sprint_id"])) {
            if ($validatedData['sprint_id'] == null) {
                $task->sprint_id = null;
            } else if ($project->hasSprintWithId($validatedData['sprint_id'])) {
                $task->sprint_id = $validatedData["sprint_id"];
            } else {
                return response()->json(['message' => 'Sprint does not belong to the specified project.'], 403);
            }
        }

        // Update the task
        $task->update($validatedData);
        return response()->json(['message' => 'Task updated successfully.']);
    }

    public function remove(Request $request, Workspace $workspace, Project $project, Task $task)
    {
        $user = $request->user();

        $errorMessage = null;

        if (!$user->hasWorkspace($workspace)) {
            $errorMessage = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $errorMessage = $this->missingProjectInWorkspaceError;
        } elseif (!$project->hasTask($task)) {
            $errorMessage = $this->missingTaskInProjectError;
        }

        if ($errorMessage) {
            return $errorMessage;
        }

        // Delete the task
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }
}
