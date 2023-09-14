<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteProjectApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Workspace $workspace;
    protected Project $project;
    


    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
        $this->artisan('db:seed');
        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id);
        $this->project = Project::factory()->for($this->workspace)->create();
        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }
    private function generateUrl($workspaceId, $projectId)
    {
        return "/api/workspaces/$workspaceId/project/$projectId";
    }
    public function testDeleteProjectSuccessfully()
    {
        $response = $this->delete($this->generateUrl($this->workspace->id, $this->project->id), [], $this->header);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Projet supprimé.']);
    }
    
    public function test_delete_project_with_invalid_workspace()
    {
        $response = $this->delete($this->generateUrl($this->unlink_workspace->id, $this->project->id), [], $this->header);
        $response->assertStatus(403);
    }
    public function test_delete_project_with_unlinked_project()
    {
        $response = $this->deleteJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id),
            [],
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals(
            $response->json("message"),
            "This project does not belong to the specified workspace."
        );
    }
}
