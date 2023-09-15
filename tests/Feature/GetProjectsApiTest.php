<?php

use App\Http\Resources\ProjectCollection;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\Feature\base\BaseTestCase;
use Tests\TestCase;

class GetProjectsApiTest extends BaseTestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * Test getting projects for a workspace.
     *
     * @return void
     */
    public function test_get_projects_for_workspace()
    {
        // Create a user, workspace and projects
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create(['created_by_id' => $user->id]);
        $workspaceId = $workspace->id;
        $user->workspaces()->attach($workspace);
        $project1 = Project::factory()->create(['workspace_id' => $workspace->id]);
        $project2 = Project::factory()->create(['workspace_id' => $workspace->id]);

        // Authenticate the user
        $this->actingAs($user);

        // Make API request to get projects for workspace
        $response = $this->getJson("/api/workspaces/$workspaceId/projects/", ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert successful response and correct projects data
        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJson((new ProjectCollection([$project1, $project2]))->response()->getData(true));
    }

    /**
     * Test getting projects for a workspace the user is not a member of.
     *
     * @return void
     */
    public function test_get_projects_for_non_member_workspace()
    {
        // Create a user, workspace and projects
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;
        $workspace = Workspace::factory()->create();

        // Authenticate the user
        $this->actingAs($user);

        // Make API request to get projects without authentication
        $response = $this->getJson("/api/workspaces/{$workspace->id}/projects/", ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        // Assert forbidden response
        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    /**
     * Test getting projects without authentication.
     *
     * @return void
     */
    public function test_get_projects_without_authentication()
    {
        // Create a workspace and projects
        $workspace = Workspace::factory()->create();
        $workspaceId = $workspace->id;
        $project1 = Project::factory()->create(['workspace_id' => $workspace->id]);
        $project2 = Project::factory()->create(['workspace_id' => $workspace->id]);

        // Make API request to get projects without authentication
        $response = $this->getJson("/api/workspaces/$workspaceId/projects/");

        // Assert unauthorized response
        $response->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
