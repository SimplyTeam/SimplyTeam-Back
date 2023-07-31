<?php

namespace Tests\Feature;

use App\Models\User;
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
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl()
    {
        return "/api/quests";
    }

    public function testGetValidQuestsIfUserHasBeenCreatedOnly()
    {

        $request = $this->getJson(
            $this->generateUrl(),
            $this->header
        );

        dd($request);
    }
}
