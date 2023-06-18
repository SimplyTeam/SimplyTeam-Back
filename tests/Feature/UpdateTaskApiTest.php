<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use DateTime;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTaskApiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

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
        $this->sprint = Sprint::factory([
            "begin_date" => $beginDate,
            "end_date" => $endDate
        ])->for($this->project)->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create(); // Make a sprint but don't save it to database

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId, $sprintId, $taskId)
    {
        return "/api/workspaces/{$workspaceId}/projects/{$projectId}/sprints/{$sprintId}/tasks/{$taskId}";
    }

    /**
     * Allows to get generated data of body
     * @throws Exception
     */
    private function getGeneratedData() {
        $beginDate = new DateTime($this->sprint->begin_date);
        $endDate = new DateTime($this->sprint->end_date);

        return [
            'label' => $this->faker->text,
            'description' => $this->faker->text,
            'estimated_timestamp' => $this->faker->randomDigitNotNull,
            'realized_timestamp' => $this->faker->randomDigitNotNull,
            'deadline' => $this->faker->dateTimeBetween($beginDate, $endDate)->format('Y-m-d H:i:s'),
            'is_finish' => false,
            'priority_id' => random_int(1, 3),
            'status_id' => random_int(1, 3)
        ];
    }

    public function testSuccessfulTaskUpdate()
    {
        $task = Task::factory()->for($this->sprint)->create();

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->sprint->id, $task->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);
    }
}
