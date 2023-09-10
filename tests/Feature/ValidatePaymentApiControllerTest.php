<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\base\BaseTestCase;

class ValidatePaymentApiControllerTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->headers = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    public function getUrl() {
        return "/api/validatePayment";
    }

    /** @test */
    public function it_can_validate_payment_and_update_user()
    {
        $user = $this->user;

        $response = $this->actingAs($user)
            ->postJson($this->getUrl(), [
                'premium_expiration_date' => now()->addMonth(), // A future date
            ], $this->headers);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Subscription has been successful set']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'premium_expiration_date' => now()->addMonth()->toDateString(),
        ]);
    }

    /** @test */
    public function it_requires_premium_expiration_date()
    {
        $user = $this->user;

        $response = $this->actingAs($user)
            ->postJson($this->getUrl(), [], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['premium_expiration_date']);
    }
}
