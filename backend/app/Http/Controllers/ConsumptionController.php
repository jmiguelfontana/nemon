<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Consumption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ConsumptionController extends Controller
{
    #[OA\Get(
        path: '/api/consumptions',
        summary: 'Obtiene los registros de consumo por hora',
        description: 'Retorna los registros de consumo en kWh para cada hora (h1 a h25), opcionalmente filtrados por rango de fechas.',
        operationId: 'getConsumptions',
        tags: ['Consumos'],
        parameters: [
            new OA\Parameter(name: 'start_date', in: 'query', required: true, description: 'Fecha inicio YYYY-MM-DD', schema: new OA\Schema(type: 'string', format: 'date', example: '2025-03-01')),
            new OA\Parameter(name: 'end_date', in: 'query', required: true, description: 'Fecha fin YYYY-MM-DD', schema: new OA\Schema(type: 'string', format: 'date', example: '2025-03-31')),
        ],
        responses: [
            new OA\Response(
                response: 422,
                description: 'Unprocessable Entity - Faltan fechas o el formato es inválido',
            ),
            new OA\Response(
                response: 200,
                description: 'Lista de consumos por fecha y horas h1..h25',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-03-01'),
                            new OA\Property(property: 'h1', type: 'number', format: 'float', example: 0.27),
                            new OA\Property(property: 'h24', type: 'number', format: 'float', example: 0.45),
                            new OA\Property(property: 'h25', type: 'number', format: 'float', nullable: true, example: null),
                        ]
                    )
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $consumptions = Consumption::query()
            ->betweenDates($validated['start_date'], $validated['end_date'])
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($consumptions, 200);
    }
}
