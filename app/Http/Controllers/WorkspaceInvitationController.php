<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptWorkspaceInvitationRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\LinkBetweenUsersAndWorkspaces;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Http\Request;

class WorkspaceInvitationController extends Controller
{
    public function accept(AcceptWorkspaceInvitationRequest $request)
    {
        $invitation = WorkspaceInvitation::where('token', $request->input('token'))->firstOrFail();
        $user = $request->user();

        // Check if the email of the invitation matches the email of the authenticated user
        if ($invitation->email !== $user->email) {
            return response()->json(['error' => 'Action non autorisée.'], 403);
        }

        $workspace = Workspace::find($invitation->workspace_id);

        // Add the user to the workspace
        $workspace->users()->attach($user);

        LinkBetweenUsersAndWorkspaces::query()
            ->where('user_id', '=', $user->id)
            ->where('workspace_id', '=', $workspace->id)
            ->update(['is_PO' => $invitation->is_PO]);

        // Delete the invitation
        $invitation->delete();

        return new WorkspaceResource($workspace);
    }
}
