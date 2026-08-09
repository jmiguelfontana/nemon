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

