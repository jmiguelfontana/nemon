<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'NEMON - API REST de Precio Indexado de Energía',
    description: 'API REST para el cálculo de precio indexado de energía evaluando fórmulas personalizadas sobre consumos y precios horarios OMIE_MD.'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Servidor Local de Desarrollo'
)]
abstract class Controller
{
    //
}
