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
        parent::setUp(); // TODO: Change the autogenerated stub
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
            ->for($this->sprint)
            ->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create(); // Make a sprint but don't save it to database

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId, $sprintId)
    {
        return "/api/workspaces/$workspaceId/projects/$projectId/sprints/$sprintId/tasks";
    }

    public function testListTasks()
    {
        // Test without any filters or sorting
        $response = $this->get(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id),
            $this->header
        );
        $response->assertStatus(200);

        $expectedResponse = collect($this->tasks->toArray())->map(function ($task) {
            return array_merge($task, ['id' => 15]);
        })->each(function (&$task) {
            unset($task['id']);
        })->toArray();
        $response->assertJson($expectedResponse);
    }

    public function testListTasksWithFilters()
    {
        // Test with filters
        $filters = [
            'status=1',
            'priority=3'
        ];
        $response = $this->get($this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id) . '?' . join('&', $filters), $this->header);

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

        // Assert that all tasks are assigned to the expected user
        $this->assertTrue(collect($responseData)->pluck('assigned_to')->every(function ($assignedTo) {
            return $assignedTo === 'john@example.com'; // Replace with the expected user email
        }));
    }

    public function testListTasksWithSorting()
    {
        // Test with sorting
        $sorting = [
            'sort_field=deadline',
            'sort_order=asc'
        ];
        $response = $this->get($this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id) . '?' . join('&', $sorting), $this->header);
        $response->assertStatus(200);
        $responseData = $response->json();
        $sortedDeadlines = collect($responseData)->pluck('deadline')->sort();

        $this->assertEquals($sortedDeadlines->values()->all(), collect($responseData)->pluck('deadline')->all());

    }

    public function test_get_task()
    {
        $sprint = Sprint::factory()->for($this->sprint)->create();

        $response = $this->getJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id),
            $this->header
        );

        $response->assertOk();
        $response->assertJsonFragment($sprint->toArray());
    }

    /**
     * Test get task with missing workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_get_task_with_missing_workspace()
    {
        $missing_workspace = Workspace::factory()->make();

        $response = $this->getJson(
            $this->generateUrl($missing_workspace->id, $this->project->id, $this->sprint->id),
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
        $this->assertEquals($response->json("message"), "This project does not belong to the specified workspace.");
    }

    public function test_get_task_with_unlinked_sprint()
    {
        $response = $this->getJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->unlink_sprint->id),
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals($response->json("message"), "This sprint does not belong to the specified project.");
    }
}
