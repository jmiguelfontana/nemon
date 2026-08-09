<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'NEMON Energy Indexing API',
        'status' => 'online',
        'version' => '1.0.0',
        'documentation' => url('/api/documentation'),
    ]);
});

// Sobrescribir la ruta interna de Swagger para forzar que el archivo JSON vaya en la URL (/docs/api-docs.json)
// y no como un parámetro de búsqueda (?api-docs.json), lo que puede ser bloqueado por Cloudflare WAF.
Route::get('docs/api-docs.json', '\L5Swagger\Http\Controllers\SwaggerController@docs')->name('l5-swagger.default.docs');
