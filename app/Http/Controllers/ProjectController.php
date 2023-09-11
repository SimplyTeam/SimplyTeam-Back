<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFormRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use App\Services\ProjectService;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    private JsonResponse $missingWorkspaceInUserError;
    private JsonResponse $missingProjectInWorkspaceError;

     public function __construct() {
        $this->middleware('auth:api');
        $this->missingWorkspaceInUserError = response()
            ->json(['message' => 'This workspace does not belong to the authenticated user.'], 403);
        $this->missingProjectInWorkspaceError = response()
            ->json(['message' => 'This project does not belong to the specified workspace.'], 403);
     }

    protected WorkspaceService $workspaceService;

    public function __construct()
    {
        $this->workspaceService = new WorkspaceService();
    }

    /**
     * Display a listing of the projects for a user in the given workspace.
     *
     * @param Request $request
     * @param Workspace $workspace
     * @return ProjectCollection|JsonResponse
     */
    public function index(Request $request, Workspace $workspace)
    {
        $user = $request->user();

        // Check that the workspace belongs to the user
        if (!$user->workspaces->contains($workspace)) {
            return response()->json(['message' => 'Workspace non trouvé.'], 404);
        }

        // Get the projects associated with the workspace
        $projects = $workspace->projects;

        // Return the projects as a resource collection
        return new ProjectCollection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Workspace $workspace, ProjectFormRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        $projectService = new ProjectService();
        if (!$projectService->isUserAllowedToCreateProjectInWorkspaceWithSubscription($user, $workspace)) {
            return Response()->json([
                "message" => "L'utilisateur ne peut pas créer plus de 2 projets dans l'espace de travail. Si vous souhaitez le faire, veuillez vous abonner à la version premium !"
            ], 402);
        }

        if($user->id != $workspace->created_by_id && !$this->workspaceService->checkIfUserIsPOOfWorkspace($user, $workspace)){
            return Response()->json([
                "message" => "You cannot create project if you are not PO of owner of selected workspace!"
            ], Response::HTTP_FORBIDDEN);
        }

        $projet = Project::create(
            $validatedData + [
                "workspace_id" => $workspace->id
            ]
        );

        return (new ProjectResource($projet))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace, Project $project)
    {
        $projetdata = $project;
        return (new ProjectResource($projetdata))->response()->setStatusCode(201);        ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectFormRequest $request, Workspace $workspace, Project $project)
    {
        $user = $request->user();

        // Check if user can update workspace
        if( $user->id != $workspace->created_by_id ||
            (!$workspace->projects->contains($project) &&
            !$this->workspaceService->checkIfUserIsPOOfWorkspace($user, $workspace))
        ){
            return Response()->json([
                "messages" => "L'utilisateur n'a pas accès à ce projet ou ne possède pas les droits nécessaires !"
            ], Response::HTTP_FORBIDDEN);
        }

        // Check that the project belongs to the workspace
        if ($project->workspace_id !== $workspace->id) {
            return Response()->json(["messages" => "Le projet ne fait pas parti du workspace renseigné !"],
                Response::HTTP_FORBIDDEN);
        }

        $project->update($request->validated());

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Workspace $workspace, Project $project)
    {
        $user = $request->user();

        $errorMessage = null;

        if (!$user->hasWorkspace($workspace)) {
            $errorMessage = $this->missingWorkspaceInUserError;
        } elseif (!$workspace->hasProject($project)) {
            $errorMessage = $this->missingProjectInWorkspaceError;
        }

        if ($errorMessage) {
            return $errorMessage;
        }

        // Delete the task
        $project->delete();

        return response()->json(['message' => 'Projet supprimé.'], 200);
    }
}
