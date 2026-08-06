<?php

use App\Http\Controllers\CalculateController;
use App\Http\Controllers\ConsumptionController;
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - NEMON Energy Indexing
|--------------------------------------------------------------------------
*/

Route::post('/calculate', CalculateController::class);

Route::get('/consumptions', ConsumptionController::class);

Route::get('/prices', PriceController::class);
