<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InfoApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_route_info_return_levels_information(): void
    {
        $response = $this->get('/info');

        $response->assertStatus(200);
    }
}
