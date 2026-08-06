<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds for March 2025.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2025, 3, 1);
        $endDate = Carbon::create(2025, 3, 31);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            $data = [
                'date' => $formattedDate,
            ];

            // Generar precios OMIE_MD representativos (€/kWh) para h1..h24
            for ($h = 1; $h <= 24; $h++) {
                // Curva de precios: horas de la madrugada más baratas, picos solares u horas punta
                $basePrice = match (true) {
                    $h >= 1 && $h <= 7 => 0.045 + ($h * 0.003),
                    $h >= 8 && $h <= 14 => 0.085 + (($h - 7) * 0.005),
                    $h >= 15 && $h <= 18 => 0.065 + (($h - 14) * 0.004),
                    $h >= 19 && $h <= 22 => 0.115 - (($h - 18) * 0.006),
                    default => 0.055,
                };

                // Variación determinista por día del mes
                $dayModifier = (($date->day % 7) * 0.004);
                $data["h{$h}"] = round($basePrice + $dayModifier, 6);
            }

            // h25 es opcional (solo en días DST de 25h)
            $data['h25'] = null;

            Price::updateOrCreate(
                ['date' => $formattedDate],
                $data
            );
        }
    }
}
