<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Http\Resources\WorkspaceCollection;
use App\Http\Resources\WorkspaceResource;
use App\Mail\WorkspaceInvitationEmail;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Workspaces",
 *     description="API Endpoints for Managing Workspaces"
 * )
 */
class WorkspaceApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/workspaces",
     *     tags={"Workspaces"},
     *     summary="List workspaces for the authenticated user",
     *     @OA\Response(
     *         response=200,
     *         description="List of workspaces for the authenticated user",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Workspace"))
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $workspaces = $user->workspaces()->orderBy('updated_at', 'desc')->get();

        return new WorkspaceCollection($workspaces);
    }

    /**
     * @OA\Post(
     *     path="/workspaces",
     *     tags={"Workspaces"},
     *     summary="Create a new workspace and send invitations",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceFormRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Workspace successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceResource")
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function store(WorkspaceFormRequest $request)
    {
        $validatedData = $request->validated();
        $currentUserAuthenticated = $request->user();
        $workspace = Workspace::create([
            'name' => $validatedData['name'],
            'description' => $validatedData["description"] ?? null,
            'created_by_id' => $currentUserAuthenticated->id
        ]);

        $workspace->users()->attach($currentUserAuthenticated);

        // Create invitations for each email in the list
       $this->sendEmail($request, $workspace, $currentUserAuthenticated);

        $workspace->save();

        return (new WorkspaceResource($workspace))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/workspaces/{id}",
     *     tags={"Workspaces"},
     *     summary="Display a specific workspace",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specific workspace details",
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceResource")
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function show(Request $request, $id)
    {
        $workspace = $request->user()->workspaces()->find($id);

        if (!$workspace) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }

        return new WorkspaceResource($workspace);
    }


    /**
     * @OA\Put(
     *     path="/workspaces/{workspace}",
     *     tags={"Workspaces"},
     *     summary="Update an existing workspace and send new invitations",
     *     @OA\Parameter(
     *         name="workspace",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceFormRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workspace successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceResource")
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function update(WorkspaceFormRequest $request, Workspace $workspace)
    {
        $user = $request->user();

        if (!$workspace->users->contains($user)) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }
        $this->sendEmail($request, $workspace, $user);

        $workspace->update($request->validated());

        return new WorkspaceResource($workspace);
    }

    /**
     * @OA\Delete(
     *     path="/workspaces/{workspace}",
     *     tags={"Workspaces"},
     *     summary="Delete a workspace",
     *     @OA\Parameter(
     *         name="workspace",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Workspace successfully deleted"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function destroy(Request $request, Workspace $workspace)
    {
        $user = $request->user();

        if (!$workspace->users->contains($user)) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }

        $workspace->delete();

        return response()->json(null, 204);
    }


    /**
     * @OA\Delete(
     *     path="/workspaces/{workspace}/users/{userId}",
     *     tags={"Workspaces"},
     *     summary="Remove a specific user from a workspace",
     *     @OA\Parameter(
     *         name="workspace",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the user to remove",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User successfully removed from workspace"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function removeUser(Request $request, Workspace $workspace, $userId)
    {
        $user = $request->user();
        if (!$workspace->users->contains($user)) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }
        if ($user->id === (int)$userId) {
            return response()->json(['error' => "Vous ne pouvez pas vous retirer vous-même du workspace"], 403);
        }

        $workspace->users()->detach($userId);

        return response()->json(null, 204);
    }

    public function sendEmail($request, $workspace, $user) {
        if ($request->has('invitations')) {
           foreach ($request->input('invitations') as $email) {
               $token = Str::uuid()->toString();

               $invitation = WorkspaceInvitation::create([
                   'email' => $email,
                   'workspace_id' => $workspace->id,
                   'token' => $token
               ]);

               $invitation->invitedBy()->associate($user);
               $invitation->save();

               // Send email to the invitation
               Mail::to($email)->send(
                   new WorkspaceInvitationEmail(
                       $invitation,
                       env("REDIRECTED_URL_MAIL") . "?token=" . urlencode($token)
                   )
               );
           }
       }
   }
}
