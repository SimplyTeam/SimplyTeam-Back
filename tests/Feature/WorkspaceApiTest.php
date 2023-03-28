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

    public function test_can_list_workspaces()
    {
        $workspaces = Workspace::factory()->count(3)->create();
        $response = $this->get('/api/workspaces');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceCollection($workspaces))->response()->getData(true));
    }

    public function test_can_show_workspace()
    {
        $workspace = Workspace::factory()->create();
        $response = $this->get("/api/workspaces/{$workspace->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceResource($workspace))->response()->getData(true));
    }

    public function test_can_create_workspace()
    {
        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->postJson('/api/workspaces', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson((new WorkspaceResource(Workspace::find($response->json('id'))))->response()->getData(true));
    }

    public function test_can_update_workspace()
    {
        $workspace = Workspace::factory()->create();
        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->putJson("/api/workspaces/{$workspace->id}", $data);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceResource($workspace->refresh()))->response()->getData(true));
    }

    public function test_can_delete_workspace()
    {
        $workspace = Workspace::factory()->create();
        $response = $this->deleteJson("/api/workspaces/{$workspace->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted($workspace);
    }

    public function test_cannot_show_non_existing_workspace()
    {
        $response = $this->get('/api/workspaces/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_non_existing_workspace()
    {
        $response = $this->putJson('/api/workspaces/999', []);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_non_existing_workspace()
    {
        $response = $this->deleteJson('/api/workspaces/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_name_is_required()
    {
        $response = $this->postJson('/api/workspaces', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_name_should_not_exceed_128_characters()
    {
        $data = [
            'name' => str_repeat('a', 129),
        ];

        $response = $this->postJson('/api/workspaces', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }
}
