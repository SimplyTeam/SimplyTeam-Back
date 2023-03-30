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

class WorkspaceApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $workspaces = $user->workspaces;

        return new WorkspaceCollection($workspaces);
    }

    public function store(WorkspaceFormRequest $request)
    {
        $validatedData = $request->validated();
        $currentUserAuthenticated = $request->user();
        $workspace = Workspace::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'created_by_id' => $currentUserAuthenticated->id
        ]);

        $workspace->users()->attach($currentUserAuthenticated);

        // Create invitations for each email in the list
        if ($request->has('invitations')) {
            foreach ($request->input('invitations') as $email) {
                $token = Str::uuid()->toString();

                $invitation = WorkspaceInvitation::create([
                    'email' => $email,
                    'workspace_id' => $workspace->id,
                    'token' => $token
                ]);

                $invitation->invitedBy()->associate($currentUserAuthenticated);
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

        $workspace->save();

        return (new WorkspaceResource($workspace))->response()->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $workspace = $request->user()->workspaces()->find($id);

        if (!$workspace) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }

        return new WorkspaceResource($workspace);
    }

    public function update(WorkspaceFormRequest $request, Workspace $workspace)
    {
        $user = $request->user();

        if (!$workspace->users->contains($user)) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }

        $workspace->update($request->validated());

        return new WorkspaceResource($workspace);
    }

    public function destroy(Request $request, Workspace $workspace)
    {
        $user = $request->user();

        if (!$workspace->users->contains($user)) {
            return response()->json(['error' => "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas"], 403);
        }

        $workspace->delete();

        return response()->json(null, 204);
    }
}
