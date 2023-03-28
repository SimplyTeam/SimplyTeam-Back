<?php

namespace Tests\Feature;

use App\Http\Resources\WorkspaceCollection;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\User;

class WorkspaceApiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    public function test_can_list_workspaces()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspaces = Workspace::factory()->count(3)->create();
        $user->workspaces()->attach($workspaces);
        $this->actingAs($user);

        $response = $this->get('/api/workspaces', ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceCollection($workspaces))->response()->getData(true));
    }

    public function test_can_show_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create();
        $user->workspaces()->attach($workspace);
        $this->actingAs($user);

        $response = $this->get("/api/workspaces/$workspace->id", ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceResource($workspace))->response()->getData(true));
    }

    public function test_cannot_show_non_existing_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $this->actingAs($user);

        $response = $this->get('/api/workspaces/999', ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_cannot_show_unlinked_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;


        $workspace = Workspace::factory()->create();
        $this->actingAs($user);

        $response = $this->get("/api/workspaces/$workspace->id", ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->postJson('/api/workspaces', $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(
                (new WorkspaceResource(
                    Workspace::find($response->json('data')["id"]
                    )
                ))->response()->getData(true));
    }
}
