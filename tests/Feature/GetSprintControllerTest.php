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

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();

        $this->actingAs($this->user, 'api');
        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    public function test_get_sprint()
    {
        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->getJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints",
            $this->header
        );

        $response->assertOk();
        $response->assertJsonFragment($sprint->toArray());
    }

    public function test_get_fail_with_unlink_project()
    {
        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->getJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->unlink_project->id}/sprints",
            $this->header
        );

        $response->assertUnauthorized();
    }

    public function test_get_fail_with_unlink_workspace()
    {
        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->getJson(
            "/api/workspaces/{$this->unlink_workspace->id}/projects/{$this->project->id}/sprints",
            $this->header
        );

        $response->assertUnauthorized();
    }
}
