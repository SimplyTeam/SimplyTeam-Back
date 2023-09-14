<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Tests\Feature\base\BaseTestCase;
use function PHPUnit\Framework\assertEquals;

class SetOrUnsetPOUserOnWorkspaceApiTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->workspace = Workspace::factory()->create([
            "created_by_id" => $this->user->id
        ]);

        $this->user->workspaces()->attach($this->workspace);
        $this->user->save();
        $this->actingAs($this->user);

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    public function test_if_set_user_PO_work() {
        $user_to_assign = User::factory()->create();
        $user_to_assign->workspaces()->attach($this->workspace);
        $user_to_assign->save();

        $workspaceId = $this->workspace->id;
        $userId = $user_to_assign->id;


        $response = $this->postJson(
            "/api/workspaces/$workspaceId/users/$userId/setIsPO",
            [],
            $this->header
        );

        $response
            ->assertStatus(200)
            ->assertJson(['message' => "L'opération a été réalisé avec succès!"]);

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $this->assertEquals(true, $user->pivot->is_PO, 'User must be PO!');

    }

    public function test_if_user_cannot_set_PO_if_not_creator() {
        $user_to_assign = User::factory()->create();
        $user_to_assign->workspaces()->attach($this->workspace);
        $user_to_assign->save();

        $workspaceId = $this->workspace->id;
        $userId = $user_to_assign->id;


        $response = $this->postJson(
            "/api/workspaces/$workspaceId/users/$userId/setIsPO",
            [],
            $this->header
        );

        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'Seul le créateur du workspace peut définir les PO!']);

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $this->assertEquals(true, $user->pivot->is_PO, 'User must be PO!');

    }

    public function test_if_unset_PO_of_user_work() {
        $user_to_assign = User::factory()->create();
        $user_to_assign->workspaces()->attach($this->workspace);
        $user_to_assign->save();

        $workspaceId = $this->workspace->id;
        $userId = $user_to_assign->id;

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $user->pivot->is_PO = true;
        $user->save();

        $response = $this->postJson(
            "/api/workspaces/$workspaceId/users/$userId/unsetIsPO",
            [],
            $this->header
        );

        $response
            ->assertStatus(200)
            ->assertJson(['message' => "L'opération a été réalisé avec succès!"]);

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $this->assertEquals(false, $user->pivot->is_PO, 'User must not be PO!');
    }

    public function test_if_user_cannot_unset_PO_if_not_creator() {
        $user_to_assign = User::factory()->create();
        $user_to_assign->workspaces()->attach($this->workspace);
        $user_to_assign->save();

        $workspaceId = $this->workspace->id;
        $userId = $user_to_assign->id;

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $user->pivot->is_PO = true;
        $user->save();

        $accessToken = $user_to_assign->createToken('API Token')->accessToken;
        $header = [
            "Authorization" => "Bearer $accessToken",
            "Accept" => "application/json"
        ];

        $response = $this->postJson(
            "/api/workspaces/$workspaceId/users/$userId/unsetIsPO",
            [],
            $header
        );

        $user = $this->workspace->users()->where('user_id', $user_to_assign->id)->first();
        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'Seul le créateur du workspace peut définir les PO!']);

        $this->assertEquals(true, $user->pivot->is_PO, 'User must be PO!');
    }
}
