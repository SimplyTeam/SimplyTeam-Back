<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateSprintApiTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

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

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
    }

    public function testStore()
    {
        $end_date = $this->faker->date;
        $begin_date = $this->faker->date('Y-m-d', $end_date);

        $sprintData = [
            'name' => $this->faker->name,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        $response = $this->postJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints",
            $sprintData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertCreated();
        $response->assertJsonFragment($sprintData);
    }


    public function test_get_fail_with_unlink_project()
    {
        $end_date = $this->faker->date;
        $begin_date = $this->faker->date('Y-m-d', $end_date);

        $sprintData = [
            'name' => $this->faker->name,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->postJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->unlink_project->id}/sprints",
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertUnauthorized();
    }

    public function test_create_sprint_for_non_member_project_not_working()
    {
        $end_date = $this->faker->date;
        $begin_date = $this->faker->date('Y-m-d', $end_date);

        $sprintData = [
            'name' => $this->faker->name,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        $sprint = Sprint::factory()->for($this->project)->create();

        $response = $this->postJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->unlink_project->id}/sprints",
            $sprintData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertUnauthorized();
    }

    /**
     * Test getting projects for a workspace the user is not a member of.
     *
     * @return void
     */
    public function test_create_projects_for_non_member_workspace()
    {
        $end_date = $this->faker->date;
        $begin_date = $this->faker->date('Y-m-d', $end_date);

        $sprintData = [
            'name' => $this->faker->name,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        $response = $this->postJson(
            "/api/workspaces/{$this->unlink_workspace->id}/projects/{$this->project->id}/sprints",
            $sprintData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );
        // Assert forbidden response
        $response->assertUnauthorized();
    }

    /**
     * Test getting projects without authentication.
     *
     * @return void
     */
    public function test_create_projects_without_authentication()
    {
        $end_date = $this->faker->date;
        $begin_date = $this->faker->date('Y-m-d', $end_date);

        $sprintData = [
            'name' => $this->faker->name,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        // Make API request to get projects without authentication
        $response = $this->postJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints",
            $sprintData
        );

        // Assert unauthorized response
        $response->assertUnauthorized();
    }
}
