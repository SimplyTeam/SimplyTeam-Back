<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptWorkspaceInvitationRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Workspace Invitations",
 *     description="API Endpoints for Managing Workspace Invitations"
 * )
 */
class WorkspaceInvitationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/workspaces/invitations/accept",
     *     tags={"Workspace Invitations"},
     *     summary="Accept a workspace invitation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AcceptWorkspaceInvitationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workspace details after accepting the invitation",
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized action.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function accept(AcceptWorkspaceInvitationRequest $request)
    {
        $invitation = WorkspaceInvitation::where('token', $request->input('token'))->firstOrFail();
        $user = $request->user();

        // Check if the email of the invitation matches the email of the authenticated user
        if ($invitation->email !== $user->email) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $workspace = Workspace::find($invitation->workspace_id);

        // Add the user to the workspace
        $workspace->users()->attach($user);

        // Delete the invitation
        $invitation->delete();

        return new WorkspaceResource($workspace);
    }
}
