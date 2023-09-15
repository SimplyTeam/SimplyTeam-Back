<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Http\Resources\WorkspaceCollection;
use App\Http\Resources\WorkspaceResource;
use App\Mail\WorkspaceInvitationEmail;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Exception;

class WorkspaceApiController extends Controller
{
    private WorkspaceService $workspaceService;

    public function __construct()
    {
        $this->workspaceService = new WorkspaceService();
    }

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $workspaces = $user->workspaces()->orderBy('updated_at', 'desc')->get();

            return new WorkspaceCollection($workspaces);
        }catch (\Throwable $e) {
            return response()->json($e->getMessage());
        }
    }

    public function store(WorkspaceFormRequest $request)
    {
        $validatedData = $request->validated();
        $currentUserAuthenticated = $request->user();

        $workspaceService = new WorkspaceService();
        if (!$workspaceService->userIsAllowToCreateWorkspace($currentUserAuthenticated)) {
           return response()
               ->json(
                   ['message' => 'Vous ne pouvez pas créer plus d\'un espace de travail. ' .
                                 'Veuillez souscrire à une offre si vous voulez continuer !'],
                   402
               );
        }

        if (
            !$currentUserAuthenticated->isPremiumValid() &&
            $request->has('invitations') &&
            count($request['invitations']) > 8
        ) {
            return response()
                ->json(
                    ['message' => 'Vous ne pouvez pas inviter plus de 8 utilisateurs. '.
                                  'Veuillez passer à la version premium si vous souhaitez inviter plus de 8 utilisateurs !'],
                    402
                );
        }

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

        if (
            $request->has('invitations') &&
            $this->workspaceService->userCanInviteNUsersInWorkspaceIsAllow(
                $user,
                count($request['invitations']),
                $workspace
            )
        ) {
            return response()
                ->json(
                    ['message' => 'Vous ne pouvez pas inviter plus de 8 utilisateurs. '.
                        'Veuillez passer à la version premium si vous souhaitez inviter plus de 8 utilisateurs !'],
                    402
                );
        }

        $this->sendEmail($request, $workspace, $user);

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
            foreach ($request->input('invitations') as $invitationData) {
                $email = $invitationData['email'];
                $isPO = $invitationData['is_PO'] ?? false;
                $token = Str::uuid()->toString();

                $invitation = WorkspaceInvitation::create([
                    'email' => $email,
                    'workspace_id' => $workspace->id,
                    'is_PO' => $isPO,
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
