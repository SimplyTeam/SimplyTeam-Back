<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateProjectApiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    /**
     * Test updating a project for a workspace.
     *
     * @return void
     */
    public function test_update_project_for_workspace()
    {
        // Create a user, workspace and project
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create(['created_by_id' => $user->id]);
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        // Authenticate the user
        $this->actingAs($user);

        // Set up updated project data
        $updatedProjectData = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];

        $workspaceId = $workspace->id;
        $projectId = $project->id;

        // Make API request to update project
        $response = $this->putJson("/api/workspaces/$workspaceId/project/$projectId", $updatedProjectData, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert successful response and correct project data
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'name' => $updatedProjectData['name'],
                'description' => $updatedProjectData['description'],
                'workspace_id' => $workspace->id
            ]
        ]);
    }

    /**
     * Test updating a project without authentication.
     *
     * @return void
     */
    public function test_update_project_without_authentication()
    {
        // Create a user, workspace and project
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create(['created_by_id' => $user->id]);
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        $workspaceId = $workspace->id;
        $projectId = $project->id;

        // Set up updated project data
        $updatedProjectData = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];

        // Make API request to update project without authentication
        $response = $this->putJson("/api/workspaces/$workspaceId/project/$projectId", $updatedProjectData, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert unauthorized response
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test updating a project for a workspace the user is not a member of.
     *
     * @return void
     */
    public function test_update_project_for_non_member_workspace()
    {
        // Create a user, workspace and project
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        $workspaceId = $workspace->id;
        $projectId = $project->id;

        // Authenticate the user
        $this->actingAs($user);

        // Set up updated project data
        $updatedProjectData = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];

        // Make API request to update project for workspace user is not a member of
        $response = $this->putJson("/api/workspaces/$workspaceId/project/$projectId", $updatedProjectData, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert forbidden response
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test updating a project that does not belong to the workspace.
     *
     * @return void
     */
    public function test_update_project_not_in_workspace()
    {
        // Create a user, workspace and project
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create(['created_by_id' => $user->id]);
        $otherWorkspace = Workspace::factory()->create();
        $project = Project::factory()->create(['workspace_id' => $otherWorkspace->id]);

        $workspaceId = $workspace->id;
        $projectId = $project->id;

        // Authenticate the user
        $this->actingAs($user);

        // Set up project data
        $newProjectData = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];

        // Make API request to update project
        $response = $this->putJson("/api/workspaces/$workspaceId/project/$projectId", $newProjectData, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert forbidden response
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test updating a project.
     *
     * @return void
     */
    public function test_update_project()
    {
        // Create a user, workspace and project
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create(['created_by_id' => $user->id]);
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        $workspaceId = $workspace->id;
        $projectId = $project->id;

        // Authenticate the user
        $this->actingAs($user);

        // Set up updated project data
        $updatedProjectData = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];

        // Make API request to update project
        $response = $this->putJson("/api/workspaces/$workspaceId/project/$projectId", $updatedProjectData, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert successful response and correct project data
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'name' => $updatedProjectData['name'],
                'description' => $updatedProjectData['description'],
                'workspace_id' => $workspace->id
            ]
        ]);
    }
}
