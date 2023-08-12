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
