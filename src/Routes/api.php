<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Manufacturing\Http\Controllers\Api\ManufacturingApiController;

Route::prefix('api/v1/manufacturing')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    // Work Orders
    Route::get('work-orders', [ManufacturingApiController::class, 'index'])->name('api.v1.manufacturing.work-orders.index');
    Route::post('work-orders', [ManufacturingApiController::class, 'store'])->name('api.v1.manufacturing.work-orders.store');
    Route::get('work-orders/{workOrder}', [ManufacturingApiController::class, 'show'])->name('api.v1.manufacturing.work-orders.show');
    Route::put('work-orders/{workOrder}', [ManufacturingApiController::class, 'update'])->name('api.v1.manufacturing.work-orders.update');
    Route::delete('work-orders/{workOrder}', [ManufacturingApiController::class, 'destroy'])->name('api.v1.manufacturing.work-orders.destroy');

    // Production Orders
    Route::get('production-orders', [ManufacturingApiController::class, 'getProductionOrders'])->name('api.v1.manufacturing.production-orders.index');

    // Bills of Materials
    Route::get('boms', [ManufacturingApiController::class, 'getBoms'])->name('api.v1.manufacturing.boms.index');
});
