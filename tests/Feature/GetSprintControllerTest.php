<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetSprintControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id);
        $this->project = Project::factory()->for($this->workspace)->create();

        $this->actingAs($this->user, 'api');
        $this->accessToken = $this->user->createToken('API Token')->accessToken;
    }

    public function test_get_sprint()
    {
        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->getJson("/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints", ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]);

        $response->assertOk();
        $response->assertJsonFragment($sprint->toArray());
    }
}
