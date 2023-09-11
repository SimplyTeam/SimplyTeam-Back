<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\base\BaseTestCase;

class UpdateSprintApiTest extends BaseTestCase
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
        $this->sprint = Sprint::factory()->for($this->project)->create();
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateData()
    {
        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);
        $middleTimestamp = (strtotime($beginDate) + strtotime($endDate)) / 2;
        $middleDate = date('Y-m-d', $middleTimestamp);

        return [
            'name' => $this->faker->sentence(3),
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'closing_date' => $middleDate
        ];
    }

    public function testUpdateSprint()
    {
        $newData = $this->generateData();

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/{$this->sprint->id}",
            $newData,
            $this->header
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

        $newData = $this->generateData();

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/{$sprint->id}",
            $newData,
            $this->header
        );

        $response->assertStatus(401);
        $this->assertDatabaseMissing('sprints', $newData);
    }

    public function testUpdateSprintWithInvalidData()
    {
        $invalidData = [
            'name' => '',
            'begin_date' => 'invalid_date',
            'end_date' => 'invalid_date',
            'closing_date' => 'invalid_date',
        ];

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/{$this->sprint->id}",
            $invalidData,
            $this->header
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'begin_date', 'end_date', 'closing_date']);
    }

    public function testUpdateSprintWithNonexistentWorkspace()
    {
        $newData = $this->generateData();

        $response = $this->putJson(
            "/api/workspaces/999999/projects/{$this->project->id}/sprints/{$this->sprint->id}",
            $newData,
            $this->header
        );

        $response->assertStatus(404);
    }

    public function testUpdateSprintWithNonexistentProject()
    {
        $newData = $this->generateData();

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/999999/sprints/{$this->sprint->id}",
            $newData,
            $this->header
        );

        $response->assertStatus(404);
    }

    public function testUpdateSprintWithNonexistentSprint()
    {
        $newData = $this->generateData();

        $response = $this->putJson(
            "/api/workspaces/{$this->workspace->id}/projects/{$this->project->id}/sprints/999999",
            $newData,
            $this->header
        );

        $response->assertStatus(404);
    }
}
