<?php

namespace Tests\Feature;

use App\Models\Quest;
use App\Models\User;
use App\Models\UserQuest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\base\BaseTestCase;

class GetQuestsApiTest extends BaseTestCase
{
    use DatabaseTransactions, WithFaker;

    function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl()
    {
        return "/api/quests";
    }

    public function testGetValidQuestsIfUserHasBeenCreatedOnly()
    {
        // Create some sample data for user quests if needed (e.g., using factories)

        $user = $this->user;

        $questsWithFirstsLevelsOnly = UserQuest::query()
            ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
            ->where('user_id', '=', $user->id)
            ->orderBy('previous_quest_id')
            ->get();

        $expected_response = [];

        foreach ($questsWithFirstsLevelsOnly as $userQuest) {
            $quest = $userQuest->quest; // Assuming proper relation defined
            $questAttribute = $quest->toArray();
            $combinedAttributes = array_merge($questAttribute, [
                'completed_count' => 0,
                'in_progress' => $quest->level == 1,
                'is_completed' => false,
                'date_completed' => null,
            ]);
            $expected_response[] = $combinedAttributes;
        }

        $request = $this->getJson(
            $this->generateUrl(),
            $this->header
        );

        $request->assertStatus(200);
        $request->assertJson($expected_response);
    }
}
