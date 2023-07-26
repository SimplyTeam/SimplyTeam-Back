<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\base\BaseTestCase;

class GetSprintApiTest extends BaseTestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

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

    private function generateUrl($workspaceId, $projectId)
    {
        return "/api/workspaces/{$workspaceId}/projects/{$projectId}/sprints";
    }

    public function test_get_sprint()
    {
        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->getJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $this->header
        );

        $response->assertOk();
        $response->assertJsonFragment($sprint->toArray());
    }

    public function test_get_fail_with_unlink_project()
    {
        $response = $this->getJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id),
            $this->header
        );

        $response->assertUnauthorized();
    }

    public function test_get_fail_with_unlink_workspace()
    {
        $response = $this->getJson(
            $this->generateUrl($this->unlink_workspace->id, $this->project->id),
            $this->header
        );

        $response->assertUnauthorized();
    }
}
