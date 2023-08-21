<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetQuestControllerRequest;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\UserQuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Quests",
 *     description="API Endpoints for Managing Quests"
 * )
 */
class QuestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/quests",
     *     tags={"Quests"},
     *     summary="Retrieve a list of quests for the authenticated user based on various query parameters",
     *     @OA\Parameter(
     *         name="quest_type",
     *         in="query",
     *         required=false,
     *         description="Filter quests by quest type ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="default_order",
     *         in="query",
     *         required=false,
     *         description="Order by completion ratio (asc or desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="in_progress_only",
     *         in="query",
     *         required=false,
     *         description="Filter quests that are in progress only",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of quests for the authenticated user",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Quest"))
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(GetQuestControllerRequest $request) {
        $user = $request->user();

        $quest_type = $request->input('quest_type');
        $default_order = $request->input('default_order');
        $in_progress_only = $request->input('in_progress_only');

        $questsRequest = UserQuest::query()
            ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
            ->select([
                'quests.*',
                'users_quests.completed_count',
                'users_quests.in_progress',
                'users_quests.is_completed',
                'users_quests.date_completed',
                DB::raw('users_quests.completed_count / quests.count as completion_ratio')
            ])
            ->where('user_id', '=', $user->id);

        if($quest_type) {
            $questsRequest->where('quest_types_id', '=', $quest_type);
        }

        if($in_progress_only) {
            $questsRequest->where('quest_types_id', '=', $in_progress_only);
        }

        if($default_order) {
            $questsRequest->orderBy('completion_ratio', $default_order);
        }

        $questsRequest->orderBy('previous_quest_id', 'asc');

        return $questsRequest->get();
    }
}
