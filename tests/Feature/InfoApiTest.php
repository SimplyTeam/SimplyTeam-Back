<?php

namespace Tests\Feature;

use App\Enums\UserLevelOfAuthenticatedEnum;
use App\Models\Level;
use App\Models\User;
use Tests\Feature\base\BaseTestCase;

class InfoApiTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    /**
     * A basic feature test example.
     */
    public function test_route_info_return_levels_information_for_first_current_level(): void
    {
        $response = $this->getJson('/api/info', $this->header);
        $expected_levels = [
            Level::query()->where('id', '=', 1)->first()->toArray(),
            Level::query()->where('id', '=', 2)->first()->toArray(),
            Level::query()->where('id', '=', 3)->first()->toArray(),
            Level::query()->where('id', '=', 4)->first()->toArray(),
        ];

        $expected_levels[0]['status'] = UserLevelOfAuthenticatedEnum::CURRENT->value;
        $expected_levels[1]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[2]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[3]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[4]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[5]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[6]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[7]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;

        $expected_response = [
            "levels" => $expected_levels
        ];

        $response->assertStatus(200);
        $response->assertJson(
            $expected_response
        );
    }

    /**
     * A basic feature test example.
     */
    public function test_route_info_return_levels_information_when_level_of_user_is_4(): void
    {
        $this->user->level_id = 4;
        $this->user->save();

        $response = $this->getJson('/api/info', $this->header);
        $expected_levels = [
            Level::query()->where('id', '=', 1)->first()->toArray(),
            Level::query()->where('id', '=', 2)->first()->toArray(),
            Level::query()->where('id', '=', 3)->first()->toArray(),
            Level::query()->where('id', '=', 4)->first()->toArray(),
            Level::query()->where('id', '=', 5)->first()->toArray(),
            Level::query()->where('id', '=', 6)->first()->toArray(),
            Level::query()->where('id', '=', 7)->first()->toArray(),
            Level::query()->where('id', '=', 8)->first()->toArray(),
        ];

        $expected_levels[0]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[1]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[2]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[3]['status'] = UserLevelOfAuthenticatedEnum::CURRENT->value;
        $expected_levels[4]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[5]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[6]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;
        $expected_levels[7]['status'] = UserLevelOfAuthenticatedEnum::FUTURE->value;

        $expected_response = [
            "levels" => $expected_levels
        ];

        $response->assertStatus(200);
        $response->assertJson(
            $expected_response
        );
    }

    /**
     * A basic feature test example.
     */
    public function test_route_info_return_levels_information_when_level_of_user_is_latest(): void
    {
        $max_level_id = Level::query()->max('id');
        $this->user->level_id = $max_level_id;
        $this->user->save();

        $response = $this->getJson('/api/info', $this->header);
        $expected_levels = Level::query()
            ->where('id', '<=', $max_level_id)
            ->where('id', '>', $max_level_id-8)
            ->get()
            ->toArray();

        $expected_levels[0]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[1]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[2]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[3]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[4]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[5]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[6]['status'] = UserLevelOfAuthenticatedEnum::PASSED->value;
        $expected_levels[7]['status'] = UserLevelOfAuthenticatedEnum::CURRENT->value;

        $expected_response = [
            "levels" => $expected_levels
        ];

        $response->assertStatus(200);
        $response->assertJson(
            $expected_response
        );
    }
}
