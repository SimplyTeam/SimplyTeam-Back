<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Level\LevelUpdater;
use App\Models\Level\UserRewardAssignment;
use App\Models\Project;
use App\Models\Quest;
use App\Models\Task;
use App\Models\User;
use App\Models\UserQuest;
use App\Models\Workspace;
use App\Services\QuestService;
use App\Services\WorkspaceService;
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
    private QuestService $questService;
    protected WorkspaceService $workspaceService;
    private JsonResponse $taskIsFinish;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->missingWorkspaceInUserError = response()
            ->json(['message' => 'Cet espace de travail n\'appartient pas à l\'utilisateur authentifié.'], 403);
        $this->missingProjectInWorkspaceError = response()
            ->json(['message' => 'Ce projet n\'appartient pas à l\'espace de travail spécifié.'], 403);
        $this->sprintMissingInProjectError = response()
            ->json(['message' => 'Ce sprint n\'appartient pas au projet spécifié.'], 403);
        $this->missingTaskInProjectError = response()
            ->json(['message' => 'Cette tâche n\'appartient pas au projet spécifié.'], 403);
        $this->taskIsFinish = response()
            ->json(['message' => 'Cette tâche est déjà terminée.'], 409);
        $this->userIsNotPOOrCreatorOfWorkspace = response()
            ->json(['message' => "Vous ne pouvez pas terminer la tâche si vous n'êtes pas le propriétaire ou le PO (Product Owner) de l'espace de travail !"], 409);

        $this->workspaceService = new WorkspaceService();
        $this->questService = new QuestService();
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

        $tasks = $tasks->where('project_id', $project->id)->get();

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

        return response()->json(['message' => 'Tâche créée avec succès!', 'task' => $task], 201);
    }

    public function update(UpdateTaskRequest $request, Workspace $workspace, Project $project, Task $task)
    {
        $user = $request->user();
        $responseError = null;
        $newReward = null;
        $validatedData = $request->validated();

        if (!$user->hasWorkspace($workspace)) {
            $responseError = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $responseError = $this->missingProjectInWorkspaceError;
        } elseif (
            in_array('is_finish', $validatedData) &&
            $validatedData['is_finish'] &&
            $workspace->created_by_id != $user->id &&
            !$this->workspaceService->checkIfUserIsPOOfWorkspace($user, $workspace)
        ) {
            $responseError = $this->userIsNotPOOrCreatorOfWorkspace;
        } elseif (!$project->hasTask($task)) {
            $responseError = $this->missingTaskInProjectError;
        } elseif($task['is_finish']) {
            $responseError = $this->taskIsFinish;
        }

        if ($responseError) {
            return $responseError;
        }

        $deadline = $task->deadline ? $task->deadline : null;

        if (in_array('is_finish', $validatedData) && $validatedData['is_finish']) {
            $validatedData['finished_at'] = date('Y-m-d H:i:s');

            $this->questService->updateQuest($user, 'Travail Dur', $validatedData['finished_at']);
            $this->questService->updateQuest($user, 'Maitre du temps', $validatedData['finished_at'], $deadline);

            $levelUpdater = new LevelUpdater($user);
            $levelUpdater->updateLevel();

            $userRewardAssignment = new UserRewardAssignment($user);
            $newReward = $userRewardAssignment->assignAllowedRewardOnUser();
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

        $response = ['message' => 'Tâche modifiée avec succès'];

        if($newReward) {
            $response['gain_reward'] = $newReward;
        }

        return response()->json($response);
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

        return response()->json(['message' => 'Tâche supprimée avec succès.'], 200);
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

        throw new Exception('Le sprint n\'appartient pas au projet spécifié.');
    }

    private function assignParentToTask(Task $task, ?int $parentId)
    {
        if ($parentId == null) {
            $task->parent_id = null;

            return $task;
        }

        $parentTask = Task::findOrFail($parentId);

        if ($parentTask->parent_id != null) {
            throw new Exception(
                'Impossible d\'ajouter une tâche avec une sous-tâche, en tant que sous-tâche'
            );
        }

        $task->parent_id = $parentId;
        $task->sprint_id = $parentTask->sprint_id;

        return $task;
    }
}
