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

class DeleteTaskApiTest extends TestCase
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

        $this->task = Task::factory()
            ->for($this->project)
            ->for($this->sprint)
            ->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        // Make a sprint but don't save it to database
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create();
        $this->unlink_task = Task::factory()
            ->for($this->unlink_project)
            ->for($this->unlink_sprint)
            ->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId, $taskId)
    {
        return "/api/workspaces/$workspaceId/projects/$projectId/tasks/$taskId";
    }

    public function testDeleteTaskWorkSuccessfully()
    {
        // Test without any filters or sorting
        $response = $this->deleteJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->task->id),
            [],
            $this->header
        );

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Tâche supprimée avec succès.']);
    }

    /**
     * Test delete task with missing workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_delete_task_with_missing_workspace()
    {
        $missingWorkspace = Workspace::factory()->make();

        $response = $this->deleteJson(
            $this->generateUrl($missingWorkspace->id, $this->project->id, $this->task->id),
            [],
            $this->header
        );

        $response->assertStatus(404);
    }

    /**
     * Test delete task with unlink workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_delete_task_with_unlink_workspace()
    {
        $response = $this->deleteJson(
            $this->generateUrl($this->unlink_workspace->id, $this->project->id, $this->task->id),
            [],
            $this->header
        );

        $response->assertStatus(403);
    }

    public function test_delete_task_with_unlinked_project()
    {
        $response = $this->deleteJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id, $this->task->id),
            [],
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals(
            $response->json("message"),
            "Ce projet n'appartient pas à l'espace de travail spécifié."
        );
    }

    public function test_delete_task_with_unlinked_task()
    {
        $response = $this->deleteJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->unlink_task->id),
            [],
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals(
            $response->json("message"),
            "Cette tâche n'appartient pas au projet spécifié."
        );
    }
}
