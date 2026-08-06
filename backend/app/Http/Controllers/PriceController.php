<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PriceController extends Controller
{
    #[OA\Get(
        path: '/api/prices',
        summary: 'Obtiene los registros de precios OMIE_MD por hora',
        description: 'Retorna los precios en €/kWh para cada hora (h1 a h25), opcionalmente filtrados por rango de fechas.',
        operationId: 'getPrices',
        tags: ['Precios OMIE'],
        parameters: [
            new OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Fecha inicio YYYY-MM-DD', schema: new OA\Schema(type: 'string', format: 'date', example: '2025-03-01')),
            new OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'Fecha fin YYYY-MM-DD', schema: new OA\Schema(type: 'string', format: 'date', example: '2025-03-31')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de precios por fecha y horas h1..h25',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-03-01'),
                            new OA\Property(property: 'h1', type: 'number', format: 'float', example: 0.048),
                            new OA\Property(property: 'h24', type: 'number', format: 'float', example: 0.055),
                            new OA\Property(property: 'h25', type: 'number', format: 'float', nullable: true, example: null),
                        ]
                    )
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Price::query();

        if (is_string($startDate) && is_string($endDate) && $startDate !== '' && $endDate !== '') {
            $query->betweenDates($startDate, $endDate);
        }

        $prices = $query->orderBy('date', 'asc')->get();

        return response()->json($prices, 200);
    }
}
