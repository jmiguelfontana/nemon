<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Consumption;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ConsumptionSeeder extends Seeder
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

            // Generar consumos reales aproximados (en kWh) para h1..h24
            for ($h = 1; $h <= 24; $h++) {
                // Curva de consumo típica: más baja de noche (h1-h6), picos al mediodía (h13-h15) y noche (h20-h22)
                $baseConsumption = match (true) {
                    $h >= 1 && $h <= 6 => 0.25 + ($h * 0.02),
                    $h >= 7 && $h <= 12 => 0.85 + (($h - 6) * 0.10),
                    $h >= 13 && $h <= 15 => 1.45 + (($h - 12) * 0.05),
                    $h >= 16 && $h <= 19 => 1.10 + (($h - 15) * 0.08),
                    $h >= 20 && $h <= 22 => 1.60 - (($h - 19) * 0.10),
                    default => 0.45,
                };

                // Variación determinista por día del mes
                $dayModifier = (($date->day % 5) * 0.05);
                $data["h{$h}"] = round($baseConsumption + $dayModifier, 4);
            }

            // h25 es opcional (solo en días DST de 25h)
            $data['h25'] = null;

            Consumption::updateOrCreate(
                ['date' => $formattedDate],
                $data
            );
        }
    }
}
