<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Quest;
use App\Models\Task;
use App\Models\User;
use App\Models\UserQuest;
use App\Models\Workspace;
use Carbon\Carbon;
use Exception;
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
        $this->taskIsFinish = response()
            ->json(['message' => 'This task is already finished'], 409);
    }

    public function index(Request $request, Workspace $workspace, Project $project)
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

        $tasks = Task::query()
            ->with(['sprint', 'parent'])
            ->whereNull('parent_id');

        // Apply filters
        $filters = $request->only(['status', 'priority', 'assigned_to', 'search', 'limit']);
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

    public function backlog(Request $request, Workspace $workspace, Project $project)
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

        $tasks = $project->backlog()->with(['users', 'createdBy', 'subtasks'])->get();

        return response()->json($tasks);
    }

    /**
     * @throws Exception
     */
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
            try {
                $this->assignSprintToTask($project, $task, $validatedData["sprint_id"]);

                // Remove sprint_id from validated data, to keep it from being override
                unset($validatedData["sprint_id"]);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }

        if (isset($validatedData["parent_id"])) {
            try {
                $this->assignParentToTask($task, $validatedData["parent_id"]);

                // Remove parent_id from validated data, to keep it from being override
                unset($validatedData["parent_id"]);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
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
        } elseif($task['is_finish']) {
            $responseError = $this->taskIsFinish;
        }

        if ($responseError) {
            return $responseError;
        }

        $validatedData = $request->validated();

        $deadline = $task->deadline ? $task->deadline : null;

        if (in_array('is_finish', $validatedData)) {
            $validatedData['finished_at'] = date('Y-m-d H:i:s');

            $currentQuest = $user->quests()
                ->where('name', 'Maitre du temps')
                ->where('users_quests.in_progress', true)
                ->first();

            if ($currentQuest && ($validatedData['finished_at'] < $deadline || $deadline == null)) {
                $currentQuest->pivot->completed_count++;
                $currentQuest->pivot->is_completed = $currentQuest->pivot->completed_count == $currentQuest->count;
                $currentQuest->pivot->date_completed = $currentQuest->pivot->is_completed ? Carbon::now() : null;
                $currentQuest->pivot->in_progress = !$currentQuest->pivot->is_completed;
                $currentQuest->pivot->save();

                if ($currentQuest->level < 20 && $currentQuest->pivot->is_completed) {
                    UserQuest::query()
                        ->join('quests', 'users_quests.quest_id', '=', 'quests.id')
                        ->where('user_id', $user->id)
                        ->where('name', 'Maitre du temps')
                        ->where('users_quests.in_progress', false)
                        ->where('level', $currentQuest->level + 1)
                        ->update(['in_progress' => true, 'completed_count' => 0]);
                }

                if ($currentQuest->pivot->is_completed) {
                    $user->earned_points += $currentQuest->reward_points;
                }
            }

            $currentQuest = $user->quests()
                ->where('name', 'Travail Dur')
                ->where('users_quests.in_progress', true)
                ->first();

            if ($currentQuest) {
                $currentQuest->pivot->completed_count++;
                $currentQuest->pivot->is_completed = $currentQuest->pivot->completed_count == $currentQuest->count;
                $currentQuest->pivot->date_completed = $currentQuest->pivot->is_completed ? Carbon::now() : null;
                $currentQuest->pivot->in_progress = !$currentQuest->pivot->is_completed;
                $currentQuest->pivot->save();

                if ($currentQuest->level < 20 && $currentQuest->pivot->is_completed) {
                    UserQuest::query()
                        ->join('quests', 'users_quests.quest_id', '=', 'quests.id')
                        ->where('user_id', $user->id)
                        ->where('name', 'Travail Dur')
                        ->where('users_quests.in_progress', false)
                        ->where('level', $currentQuest->level + 1)
                        ->update(['in_progress' => true, 'completed_count' => 0]);
                }

                if ($currentQuest->pivot->is_completed) {
                    $user->earned_points += $currentQuest->reward_points;
                }
            }
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
            try {
                $this->assignSprintToTask($project, $task, $validatedData["sprint_id"]);

                foreach ($task->subtasks as $subtask) {
                    $this->assignSprintToTask($project, $subtask, $validatedData["sprint_id"]);
                }

                // Remove sprint_id from validated data, to keep it from being updated
                unset($validatedData["sprint_id"]);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }

        if (isset($validatedData["parent_id"])) {
            try {
                $task = $this->assignParentToTask($task, $validatedData["parent_id"]);
                $task->save();

                // Remove parent_id from validated data, to keep it from being updated
                unset($validatedData["parent_id"]);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

        }

        // Update the task
        $task->update($validatedData);
        $user->save();
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

    /**
     * @throws Exception
     */
    private function assignSprintToTask(Project $project, Task $task, ?int $sprintId)
    {
        if ($sprintId == 0) {
            $task->sprint_id = null;

            return $task;
        }

        if ($project->hasSprintWithId($sprintId)) {
            $task->sprint_id = $sprintId;

            return $task;
        }

        throw new Exception('Sprint does not belong to the specified project.');
    }

    private function assignParentToTask(Task $task, ?int $parentId)
    {
        if ($parentId == null) {
            $task->parent_id = null;

            return $task;
        }

        $parentTask = Task::findOrFail($parentId);

        if ($parentTask->parent_id != null) {
            throw new Exception('Cannot add task with subtask, as subtask');
        }

        $task->parent_id = $parentId;
        $task->sprint_id = $parentTask->sprint_id;

        return $task;
    }
}
