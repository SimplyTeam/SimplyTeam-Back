<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateSprintApiTest extends TestCase
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
        $this->sprint = Sprint::factory()->for($this->project)->create();
    }

    public function testUpdateSprint()
    {
        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);
        $middleTimestamp = (strtotime($beginDate) + strtotime($endDate)) / 2;
        $middleDate = date('Y-m-d', $middleTimestamp);

        $newData = [
            'name' => $this->faker->sentence(3),
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'closing_date' => $middleDate
        ];

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/{$this->sprint->id}",
            $newData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(200);
        $this->assertEquals($newData["name"], $response->json("name"));
        $this->assertEquals($newData["begin_date"], $response->json("begin_date"));
        $this->assertEquals($newData["end_date"], $response->json("end_date"));
        $this->assertEquals($newData["closing_date"], $response->json("closing_date"));
        $this->assertDatabaseHas('sprints', $newData);
    }

    public function testUpdateSprintUnauthorized()
    {
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->for($workspace)->create();
        $sprint = Sprint::factory()->for($project)->create();

        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);

        $newData = [
            'name' => $this->faker->sentence(3),
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'closing_date' => $this->faker->date(),
        ];

        $response = $this->putJson(
            "/api/workspaces/{$workspace->id}/projects/{$project->id}/sprints/{$sprint->id}",
            $newData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(401);
        $this->assertDatabaseMissing('sprints', $newData);
    }

    public function testUpdateSprintWithInvalidData()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->for($workspace)->create();
        $sprint = Sprint::factory()->for($project)->create();

        $invalidData = [
            'name' => '',
            'begin_date' => 'invalid_date',
            'end_date' => 'invalid_date',
            'closing_date' => 'invalid_date',
        ];

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/{$this->sprint->id}",
            $invalidData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'begin_date', 'end_date', 'closing_date']);
    }

    public function testUpdateSprintWithNonexistentWorkspace()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->for($workspace)->create();
        $sprint = Sprint::factory()->for($project)->create();

        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);

        $newData = [
            'name' => $this->faker->sentence(3),
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'closing_date' => $this->faker->date(),
        ];

        $response = $this->putJson(
            "/api/workspaces/999999/projects/{$project->id}/sprints/{$sprint->id}",
            $newData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(404);
    }

    public function testUpdateSprintWithNonexistentProject()
    {
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->for($workspace)->create();
        $sprint = Sprint::factory()->for($project)->create();

        $newData = [
            'name' => $this->faker->sentence(3),
            'begin_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'closing_date' => $this->faker->date(),
        ];

        $response = $this->putJson(
            "/api/workspaces/{$workspace->id}/projects/999999/sprints/{$sprint->id}",
            $newData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(404);
    }

    public function testUpdateSprintWithNonexistentSprint()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $project = Project::factory()->for($workspace)->create();
        $sprint = Sprint::factory()->for($project)->create();

        $newData = [
            'name' => $this->faker->sentence(3),
            'begin_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'closing_date' => $this->faker->date(),
        ];

        $response = $this->putJson(
            "/api/workspaces/{$workspace->id}/projects/{$project->id}/sprints/999999",
            $newData,
            ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(404);
    }
}
