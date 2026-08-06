<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CalculateRequest;
use App\Services\EnergyCalculationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculateController extends Controller
{
    public function __construct(
        private readonly EnergyCalculationService $calculationService
    ) {}

    #[OA\Post(
        path: '/api/calculate',
        summary: 'Calcula el precio indexado de energía',
        description: 'Evalúa la fórmula configurada por el usuario sobre los precios OMIE_MD y consumos horarios almacenados en la base de datos.',
        operationId: 'calculatePrice',
        tags: ['Cálculo de Energía'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Rango de fechas y fórmula matemática con el segmento obligatorio [OMIE_MD]',
            content: new OA\JsonContent(
                required: ['start_date', 'end_date', 'formula'],
                properties: [
                    new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-03-01'),
                    new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-03-31'),
                    new OA\Property(property: 'formula', type: 'string', example: '([OMIE_MD] * 0.6) + 0.88'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cálculo realizado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'price_indexed', type: 'number', format: 'float', example: 95.4215),
                        new OA\Property(property: 'total_importes', type: 'number', format: 'float', example: 1500.50),
                        new OA\Property(property: 'total_consumos', type: 'number', format: 'float', example: 15.725),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request - Datos de entrada inválidos, incompletos o fórmula sintácticamente errónea',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'La fórmula debe incluir la etiqueta obligatoria [OMIE_MD].'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not Found - Registros de consumo o precio incompletos en el rango de fechas solicitado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'No existen registros completos de consumos o precios para la fecha: 2025-03-01'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal Server Error - Ocurrió un error inesperado al procesar los datos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Ocurrió un error interno en el servidor.'),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(CalculateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = $this->calculationService->calculate(
                $validated['start_date'],
                $validated['end_date'],
                $validated['formula']
            );

            return response()->json($result, 200);

        } catch (NotFoundHttpException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Ocurrió un error interno en el servidor.',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
