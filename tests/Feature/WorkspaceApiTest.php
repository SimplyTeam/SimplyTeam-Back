<?php

namespace Tests\Feature;

use App\Http\Resources\WorkspaceCollection;
use App\Http\Resources\WorkspaceResource;
use App\Mail\WorkspaceInvitationEmail;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\User;

class WorkspaceApiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    public function test_can_list_workspaces()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspaces = Workspace::factory()->count(3)->create([
            "created_by_id" => $user->id
        ]);

        $user->workspaces()->attach($workspaces);
        $this->actingAs($user);

        $response = $this->get('/api/workspaces', ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceCollection($workspaces))->response()->getData(true));
    }

    public function test_can_show_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);
        $user->workspaces()->attach($workspace);
        $this->actingAs($user);

        $response = $this->get("/api/workspaces/$workspace->id", ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceResource($workspace))->response()->getData(true));
    }

    public function test_cannot_show_non_existing_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $this->actingAs($user);

        $response = $this->get('/api/workspaces/999', ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_cannot_show_unlinked_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;


        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);
        $this->actingAs($user);

        $response = $this->get("/api/workspaces/$workspace->id", ["Authorization" => "Bearer $accessToken", "accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $data = [
            'name' => $this->faker->name,
            'description' => $this->faker->realText()
        ];

        $response = $this->postJson('/api/workspaces', $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(
                (new WorkspaceResource(
                    Workspace::find($response->json('id')
                    )
                ))->response()->getData(true));
    }

    public function test_can_create_workspace_with_email()
    {
        Mail::fake(); // initialisation de Mail Fake
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $dataSend = [
            'name' => $this->faker->name,
            'invitations' => [
                'user1@example.com',
                'user2@example.com'
            ]
        ];

        $response = $this->postJson('/api/workspaces', $dataSend, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(
                (new WorkspaceResource(
                    Workspace::find($response->json('id')
                    )
                ))->response()->getData(true));

        $data = $response->json();

        // Vérification que les e-mails ont été correctement créés
        Mail::assertSent(function (WorkspaceInvitationEmail $mail) use ($user, $data, $dataSend) {
            $mailData = $mail->workspaceInvitation->attributesToArray();
            return $mail->hasTo($dataSend['invitations'][0])
                && $mailData['workspace_id'] === $data['id']
                && starts_with($mail->invitationUrl, "https://example.com/invitation")
                && $mailData['invited_by_id'] === $user->id;
        });
        Mail::assertSent(function (WorkspaceInvitationEmail $mail) use ($user, $data, $dataSend) {
            $mailData = $mail->workspaceInvitation->attributesToArray();
            return $mail->hasTo($dataSend['invitations'][1])
                && $mailData['workspace_id'] === $data['id']
                && starts_with($mail->invitationUrl, "https://example.com/invitation")
                && $mailData['invited_by_id'] === $user->id;
        });
    }

    public function test_name_should_not_exceed_128_characters()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $this->actingAs($user);

        $data = [
            'name' => str_repeat('a', 129),
        ];

        $response = $this->postJson('/api/workspaces', $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);
        $user->workspaces()->attach($workspace);
        $this->actingAs($user);

        $data = [
            'name' => $this->faker->name
        ];

        $response = $this->putJson("/api/workspaces/$workspace->id", $data, ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson((new WorkspaceResource($workspace->refresh()))->response()->getData(true));
    }

    public function test_cannot_show_workspace_for_non_attached_user()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);

        $response = $this->actingAs($user)->get("/api/workspaces/$workspace->id", ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['error' => 'Vous n\'avez pas accès à ce workspace ou celui-ci n\'existe pas']);
    }

    public function test_cannot_update_workspace_for_non_attached_user()
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);

        $accessToken = $user->createToken('API Token')->accessToken;

        $response = $this->actingAs($user)->putJson("/api/workspaces/$workspace->id", ["name" => "changement"], ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['error' => 'Vous n\'avez pas accès à ce workspace ou celui-ci n\'existe pas']);
    }

    public function test_can_delete_workspace()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);

        $user->workspaces()->attach($workspace);
        $accessToken = $user->createToken('API Token')->accessToken;

        $response = $this->deleteJson("/api/workspaces/$workspace->id", [], ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted($workspace);
    }

    public function test_cannot_delete_non_existing_workspace()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $this->actingAs($user);

        $response = $this->deleteJson('/api/workspaces/999', [], ["Authorization" => "Bearer $accessToken", "Accept" => "application/json"]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_can_accept_workspace_invitation()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);

        $invitation = WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'email' => $user->email,
        ]);

        $accessToken = $user->createToken('API Token')->accessToken;

        $response = $this->postJson('/api/workspaces/invitations/accept', [
            'token' => $invitation->token,
        ], [
            'Authorization' => "Bearer $accessToken",
            'Accept' => 'application/json',
        ]);

        $createdWorkspace = Workspace::firstWhere('name', $workspace->name);

        $response->assertStatus(200)
            ->assertJson((new WorkspaceResource($workspace->refresh()))->response()->getData(true));

        $this->assertDatabaseHas('link_between_users_and_workspaces', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('workspaces_invitations', [
            'id' => $invitation->id,
        ]);
    }

    public function test_cannot_accept_workspace_invitation_with_invalid_token()
    {
        $user = User::factory()->create();
        $accessToken = $user->createToken('API Token')->accessToken;

        $response = $this->postJson('/api/workspaces/invitations/accept', [
            'token' => 'invalid-token',
        ], [
            'Authorization' => "Bearer $accessToken",
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(404);
    }

    public function test_cannot_accept_workspace_invitation_with_unauthorized_email()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workspace = Workspace::factory()->create([
            "created_by_id" => $user->id
        ]);

        $invitation = WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'email' => $otherUser->email,
        ]);

        $accessToken = $user->createToken('API Token')->accessToken;

        $response = $this->postJson('/api/workspaces/invitations/accept', [
            'token' => $invitation->token,
        ], [
            'Authorization' => "Bearer $accessToken",
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(403);
    }
}
