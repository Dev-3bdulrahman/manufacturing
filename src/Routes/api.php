<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/manufacturing')->name('api.manufacturing.')->group(function () {
    Route::get('/production-orders', [\Dev3bdulrahman\Manufacturing\Http\Controllers\Api\ManufacturingApiController::class, 'getProductionOrders']);
    Route::post('/production-orders', [\Dev3bdulrahman\Manufacturing\Http\Controllers\Api\ManufacturingApiController::class, 'createProductionOrder']);
    Route::get('/boms', [\Dev3bdulrahman\Manufacturing\Http\Controllers\Api\ManufacturingApiController::class, 'getBoms']);
});
