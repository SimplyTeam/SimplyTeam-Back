<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetQuestControllerRequest;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\UserQuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestController extends Controller
{
    public function index(GetQuestControllerRequest $request) {
        $user = $request->user();

        $quest_type = $request->input('quest_type');
        $default_order = $request->input('default_order');

        $questsRequest = UserQuest::query()
            ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
            ->select(['quests.*', DB::raw('completed_count / count as completion_ratio')])
            ->where('user_id', '=', $user->id)
            ->where('in_progress', '=', true);

        if(!$quest_type) {
            $questsRequest->where('quest_types_id', '=', $quest_type);
        }

        if(!$default_order) {
            $questsRequest->orderBy('completion_ration', $quest_type);
        }

        $questsRequest->orderBy('previous_quest_id', 'desc');

        return $questsRequest->get();
    }
}
