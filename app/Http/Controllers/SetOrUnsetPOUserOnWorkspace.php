<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class SetOrUnsetPOUserOnWorkspace extends Controller
{
    private WorkspaceService $workspaceService;

    public function __construct()
    {
        $this->workspaceService = new WorkspaceService();
    }

    /**
     * @throws \Exception
     */
    public function setIsPOOfUserOnWorkspace(Request $request, Workspace $workspace, User $user) {
        try {
            $authenticatedUser = $request->user();

            if ($workspace->created_by_id != $authenticatedUser->id) {
                return response()->json(['message' => 'Seul le créateur du workspace peut définir les PO!'], 401);
            }

            $this->workspaceService->setUserPOOnWorkspace($user, $workspace);

            return response()->json(['message' => "L'opération a été réalisé avec succès!"]);

        }catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * @throws \Exception
     */
    public function unsetIsPOOfUserOnWorkspace(Request $request, Workspace $workspace, User $user) {
        try {
            $authenticatedUser = $request->user();

            if ($workspace->created_by_id != $authenticatedUser->id) {
                return response()->json(['message' => 'Seul le créateur du workspace peut définir les PO!'], 401);
            }

            $this->workspaceService->unsetUserPOOnWorkspace($user, $workspace);

            return response()->json(['message' => "L'opération a été réalisé avec succès!"]);

        }catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
    }
}
