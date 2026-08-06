<?php

namespace Tests\Feature;

use App\Models\Consumption;
use App\Models\Price;
use App\Services\EnergyCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear registro de consumo de prueba para 2025-03-01
        $consumptionData = ['date' => '2025-03-01'];
        for ($i = 1; $i <= 24; $i++) {
            $consumptionData["h{$i}"] = 10.0; // 10 kWh cada hora
        }
        Consumption::create($consumptionData);

        // Crear registro de precio de prueba para 2025-03-01
        $priceData = ['date' => '2025-03-01'];
        for ($i = 1; $i <= 24; $i++) {
            $priceData["h{$i}"] = 0.10; // 0.10 €/kWh cada hora
        }
        Price::create($priceData);
    }

    /**
     * Test 200 OK: Cálculo realizado exitosamente.
     */
    public function test_calculate_endpoint_success_200(): void
    {
        $response = $this->postJson('/api/calculate', [
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-01',
            'formula' => '([OMIE_MD] * 0.6) + 0.88',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'price_indexed',
                'total_importes',
                'total_consumos',
            ]);

        // (0.10 * 0.6) + 0.88 = 0.94 €/kWh
        // total_consumos = 24 * 10 = 240 kWh
        // total_importes = 240 * 0.94 = 225.60 €
        // price_indexed = 225.60 / 240 = 0.94
        $this->assertEquals(0.94, $response->json('price_indexed'));
        $this->assertEquals(225.60, $response->json('total_importes'));
        $this->assertEquals(240.0, $response->json('total_consumos'));
    }

    /**
     * Test 400 Bad Request: Cuando falta la etiqueta obligatoria [OMIE_MD].
     */
    public function test_calculate_endpoint_bad_request_400_when_missing_formula_tag(): void
    {
        $response = $this->postJson('/api/calculate', [
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-01',
            'formula' => '10 + 5',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['error', 'details']);
    }

    /**
     * Test 400 Bad Request: Cuando el rango de fechas es inválido (start_date > end_date).
     */
    public function test_calculate_endpoint_bad_request_400_when_invalid_date_range(): void
    {
        $response = $this->postJson('/api/calculate', [
            'start_date' => '2025-03-10',
            'end_date' => '2025-03-01',
            'formula' => '([OMIE_MD] * 0.6) + 0.88',
        ]);

        $response->assertStatus(400);
    }

    /**
     * Test 404 Not Found: Cuando faltan registros en la base de datos para el rango.
     */
    public function test_calculate_endpoint_not_found_404_when_date_missing(): void
    {
        $response = $this->postJson('/api/calculate', [
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-02', // 2025-03-02 no existe en DB
            'formula' => '([OMIE_MD] * 0.6) + 0.88',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test 500 Internal Server Error: Cuando ocurre una excepción inesperada durante el proceso.
     */
    public function test_calculate_endpoint_internal_server_error_500(): void
    {
        // Simular un fallo inesperado del servicio inyectado
        $this->mock(EnergyCalculationService::class, function ($mock) {
            $mock->shouldReceive('calculate')
                ->andThrow(new \RuntimeException('Database failure or unhandled exception'));
        });

        $response = $this->postJson('/api/calculate', [
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-01',
            'formula' => '([OMIE_MD] * 0.6) + 0.88',
        ]);

        $response->assertStatus(500)
            ->assertJsonStructure(['error']);
    }
}
