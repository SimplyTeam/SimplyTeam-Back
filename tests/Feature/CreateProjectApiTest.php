<?php

namespace Tests\Feature;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\base\BaseTestCase;
use App\Models\User;

class CreateProjectApiTest extends BaseTestCase
{

    use DatabaseTransactions, WithFaker;

    /**
     * Test successful creation of a project.
     *
     * @return void
     */
    public function test_can_create_project()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);
        $workspace->users()->attach($user);

        $workspaceId = $workspace->id;

        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->postJson("/api/workspaces/$workspaceId/projects", $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(
                (new ProjectResource(
                    Project::find($response->json('data')["id"]
                    )
                ))->response()->getData(true));
    }

    /**
     * Test successful creation of a project.
     *
     * @return void
     */
    public function test_cannot_create_project_if_user_is_not_subscribed_and_have_2_projects()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);
        $workspace->users()->attach($user);
        $workspaceId = $workspace->id;

        $projects = Project::factory(2)->create([
            'workspace_id' => $workspaceId
        ]);

        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->postJson(
            "/api/workspaces/$workspaceId/projects",
            $data,
            ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(Response::HTTP_PAYMENT_REQUIRED)
            ->assertJson(
                [
                    'message' => "L'utilisateur ne peut pas créer plus de 2 projets dans l'espace de travail. ".
                                 "Si vous souhaitez le faire, veuillez vous abonner à la version premium !"
                ]
            );
    }

    /**
     * Test creation of a project with missing parameters.
     *
     * @return void
     */
    public function test_cannot_create_project_with_missing_parameters()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create();

        $workspaceId = $workspace->id;

        $response = $this->postJson(
            "/api/workspaces/$workspaceId/projects",
            [],
            ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test creation of a project by a user not belonging to the workspace.
     *
     * @return void
     */
    public function test_cannot_create_project_if_user_does_not_belong_to_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create();

        $workspaceId = $workspace->id;

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->postJson("/api/workspaces/$workspaceId/projects", $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(["messages" => "L'utilisateur n'a pas accès à ce projet ou ne possède pas les droits nécessaires !"]);
    }
}
