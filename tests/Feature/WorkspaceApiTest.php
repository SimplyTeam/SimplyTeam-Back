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
}
