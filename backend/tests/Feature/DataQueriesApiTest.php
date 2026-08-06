<?php

namespace Tests\Feature;

use App\Models\Consumption;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataQueriesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Consumption::create([
            'date' => '2025-03-01',
            'h1' => 1.5,
            'h2' => 2.0,
        ]);

        Price::create([
            'date' => '2025-03-01',
            'h1' => 0.05,
            'h2' => 0.06,
        ]);
    }

    public function test_get_consumptions_endpoint(): void
    {
        $response = $this->getJson('/api/consumptions?start_date=2025-03-01&end_date=2025-03-01');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'date' => '2025-03-01',
                'h1' => 1.5,
            ]);
    }

    public function test_get_prices_endpoint(): void
    {
        $response = $this->getJson('/api/prices?start_date=2025-03-01&end_date=2025-03-01');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'date' => '2025-03-01',
                'h1' => 0.05,
            ]);
    }
}
