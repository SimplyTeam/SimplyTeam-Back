<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\base\BaseTestCase;

class CreateSprintApiTest extends BaseTestCase
{

    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create(['created_by_id' => $this->user->id]);
        $this->workspace->users()->attach($this->user->id);
        $this->project = Project::factory()->for($this->workspace)->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId)
    {
        return "/api/workspaces/{$workspaceId}/projects/{$projectId}/sprints";
    }

    private function generateSprintData()
    {
        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);

        return [
            'name' => $this->faker->name,
            'begin_date' => $beginDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Test if we can store sprint
     * @return void
     */
    public function testStore()
    {
        $sprintData = $this->generateSprintData();

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $sprintData,
            $this->header
        );

        $response->assertCreated();
        $response->assertJsonFragment($sprintData);
    }

    /**
     * Test if we can create sprint for project unlink
     * @return void
     */
    public function test_create_sprint_for_project_unlink_with_workspace_fail()
    {
        $sprintData = $this->generateSprintData();

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id),
            $sprintData,
            $this->header
        );

        $response->assertUnauthorized();
    }

    /**
     * Test if we can create sprint for project unlink
     * @return void
     */
    public function test_create_sprint_for_project_with_user_not_PO_not_work()
    {
        $new_user = User::factory()->create();

        $accessToken = $new_user->createToken('API Token')->accessToken;
        $header = ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"];

        $this->workspace->users()->attach($new_user, ['is_PO' => false]);

        $sprintData = $this->generateSprintData();

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $sprintData,
            $header
        );

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'User can\'t manage sprint if he is not owner or PO of workspace']);
    }

    /**
     * Test getting projects for a workspace the user is not a member of.
     *
     * @return void
     */
    public function test_create_projects_for_non_member_workspace()
    {
        $sprintData = $this->generateSprintData();

        $response = $this->postJson(
            $this->generateUrl($this->unlink_workspace->id, $this->project->id),
            $sprintData,
            $this->header
        );

        $response->assertUnauthorized();
    }

    /**
     * Test getting projects without authentication.
     *
     * @return void
     */
    public function test_create_projects_without_authentication()
    {
        $sprintData = $this->generateSprintData();

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $sprintData
        );

        $response->assertUnauthorized();
    }
}
