<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consumption;
use App\Models\Price;

class CalculationTest extends TestCase
{
    // Limpia la BD antes de cada test para evitar que tests anteriores interfieran
    use RefreshDatabase;

    /**
     * Test the mathematical engine using controlled fake data.
     */
    public function test_math_engine_calculates_correctly(): void
    {
        // 1. Arrange: Preparar datos controlados para el día 2025-01-01
        $date = '2025-01-01';
        
        $hoursData = [];
        for ($i = 1; $i <= 25; $i++) {
            $hoursData["h$i"] = ($i <= 24) ? 10 : 0; // 10 kWh cada hora (Total 240)
        }
        
        Consumption::create(array_merge(['date' => $date], $hoursData));

        $priceData = [];
        for ($i = 1; $i <= 25; $i++) {
            $priceData["h$i"] = ($i <= 24) ? 0.10 : 0; // 0.10 €/kWh cada hora
        }
        
        Price::create(array_merge(['date' => $date], $priceData));

        // 2. Act: Enviar la petición de cálculo
        // Formula: ([OMIE_MD] * 1.5) + 0.05
        // Expected hour price: (0.10 * 1.5) + 0.05 = 0.20
        // Expected hour import: 10 * 0.20 = 2.0
        // Expected total import: 2.0 * 24 = 48.0
        // Expected total consumption: 10 * 24 = 240.0
        // Expected weighted price: 48.0 / 240.0 = 0.20
        
        $response = $this->postJson('/api/calculate', [
            'start_date' => $date,
            'end_date'   => $date,
            'formula'    => '([OMIE_MD] * 1.5) + 0.05'
        ]);

        // 3. Assert: Validar la respuesta HTTP y que los números coincidan con precisión matemática
        $response->assertStatus(200)
                 ->assertJson([
                     'total_consumos' => 240.0,
                     'total_importes' => 48.0,
                     'price_indexed'  => 0.20,
                 ]);
    }
}
