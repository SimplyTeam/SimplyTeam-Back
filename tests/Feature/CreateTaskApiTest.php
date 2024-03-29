<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\Workspace;
use DateTime;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTaskApiTest extends TestCase
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
        // Make a sprint but don't save it to database
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId)
    {
        return "/api/workspaces/{$workspaceId}/projects/{$projectId}/tasks";
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

    /**
     * Test successful task creation
     *
     * @return void
     * @throws Exception
     */
    public function testSuccessfulTaskCreation()
    {

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Tâche créée avec succès!'
            ]);
    }

    /**
     * Test task creation with missing required data
     *
     * @return void
     */
    public function testTaskCreationWithMissingData()
    {
        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            [
            ],
            $this->header
        );

        $response->assertStatus(422);  // Unprocessable Entity
        $response->assertJsonStructure([
            "message",
            "errors"
        ]);

        foreach ($response->json("errors") as $key => $value) {
            $this->assertTrue(
                in_array(
                    $key,
                    [
                        "label",
                        "description",
                        "estimated_timestamp",
                        "realized_timestamp",
                        "deadline",
                        "is_finish",
                        "priority_id",
                        "status_id"
                    ]
                )
            );
        }
    }

    /**
     * Test task creation with unauthorized user
     *
     * @return void
     * @throws Exception
     */
    public function testTaskCreationWithUnauthorizedUser()
    {
        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $this->getGeneratedData()
        );

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test store task with missing workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_store_task_with_missing_workspace()
    {
        $this->workspace = Workspace::factory()->make();

        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->project->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(404);
    }

    public function test_store_task_with_unlinked_project()
    {
        $response = $this->postJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals(
            $response->json("message"),
            "Ce projet n'appartient pas à l'espace de travail spécifié."
        );
    }
}
