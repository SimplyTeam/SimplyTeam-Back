<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetTaskApiTest extends TestCase
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

        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id);
        $this->project = Project::factory()->for($this->workspace)->create();
        $this->sprint = Sprint::factory()
            ->for($this->project)
            ->create([
                "begin_date" => $beginDate,
                "end_date" => $endDate
            ]);

        $this->tasks = Task::factory()
            ->times(5)
            ->for($this->project)
            ->for($this->sprint)
            ->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        // Make a sprint but don't save it to database
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId, $sprintId)
    {
        return "/api/workspaces/$workspaceId/projects/$projectId/tasks";
    }

    public function testListTasks()
    {
        // Test without any filters or sorting
        $response = $this->get(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id),
            $this->header
        );

        $response->assertStatus(200);

        // Get the tasks from the response
        $returnedTasks = $response->json();

        // Verify that each returned task is in $this->tasks
        foreach ($returnedTasks as $task) {
            $this->assertTrue($this->tasks->contains('label', $task['label']));
            $this->assertTrue($this->tasks->contains('description', $task['description']));
            $this->assertTrue($this->tasks->contains('estimated_timestamp', $task['estimated_timestamp']));
            $this->assertTrue($this->tasks->contains('realized_timestamp', $task['realized_timestamp']));
            $this->assertTrue($this->tasks->contains('deadline', $task['deadline']));
            $this->assertTrue($this->tasks->contains('is_finish', $task['is_finish']));
            $this->assertTrue($this->tasks->contains('priority_id', $task['priority_id']));
            $this->assertTrue($this->tasks->contains('status_id', $task['status_id']));
            $this->assertTrue($this->tasks->contains('project_id', $task['project_id']));
            $this->assertTrue($this->tasks->contains('sprint_id', $task['sprint_id']));
        }
    }


    public function testListTasksWithFilters()
    {
        // Test with filters
        $filters = [
            'status=2',
            'priority=1'
        ];
        $response = $this->get(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id) .
                '?' . join('&', $filters),
            $this->header
        );

        $response->assertStatus(200);

        $responseData = $response->json();

        // Assert that all tasks have the expected status
        $this->assertTrue(collect($responseData)->pluck('status_id')->every(function ($statusId) {
            return $statusId === 2; // Replace with the expected status ID
        }));

        // Assert that all tasks have the expected priority
        $this->assertTrue(collect($responseData)->pluck('priority_id')->every(function ($priorityId) {
            return $priorityId === 1; // Replace with the expected priority ID
        }));
    }

    public function testListTasksWithSorting()
    {
        // Test with sorting
        $sorting = [
            'sort_field=deadline',
            'sort_order=asc'
        ];
        $response = $this->get(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id)
                . '?' . join('&', $sorting),
            $this->header
        );
        $response->assertStatus(200);
        $responseData = $response->json();
        $sortedDeadlines = collect($responseData)->pluck('deadline')->sort();

        $this->assertEquals($sortedDeadlines->values()->all(), collect($responseData)->pluck('deadline')->all());

    }

    /**
     * Test get task with missing workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_get_task_with_missing_workspace()
    {
        $missingWorkspace = Workspace::factory()->make();

        $response = $this->getJson(
            $this->generateUrl($missingWorkspace->id, $this->project->id, $this->sprint->id),
            $this->header
        );

        $response->assertStatus(404);
    }

    public function test_get_task_with_unlinked_project()
    {
        $response = $this->getJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id, $this->sprint->id),
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals(
            $response->json("message"),
            "Ce projet n'appartient pas à l'espace de travail spécifié."
        );
    }
}
