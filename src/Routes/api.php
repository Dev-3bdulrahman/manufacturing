<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Manufacturing\Http\Controllers\Api\ManufacturingApiController;

Route::prefix('api/v1/manufacturing')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    // Work Orders
    Route::get('work-orders', [ManufacturingApiController::class, 'index'])->middleware('can:manufacturing.work_orders.view')->name('api.v1.manufacturing.work-orders.index');
    Route::post('work-orders', [ManufacturingApiController::class, 'store'])->middleware('can:manufacturing.work_orders.create')->name('api.v1.manufacturing.work-orders.store');
    Route::get('work-orders/{workOrder}', [ManufacturingApiController::class, 'show'])->middleware('can:manufacturing.work_orders.view')->name('api.v1.manufacturing.work-orders.show');
    Route::put('work-orders/{workOrder}', [ManufacturingApiController::class, 'update'])->middleware('can:manufacturing.work_orders.edit')->name('api.v1.manufacturing.work-orders.update');
    Route::delete('work-orders/{workOrder}', [ManufacturingApiController::class, 'destroy'])->middleware('can:manufacturing.work_orders.delete')->name('api.v1.manufacturing.work-orders.destroy');

    // Production Orders
    Route::get('production-orders', [ManufacturingApiController::class, 'getProductionOrders'])->middleware('can:manufacturing.production_orders.view')->name('api.v1.manufacturing.production-orders.index');

    // Bills of Materials
    Route::get('boms', [ManufacturingApiController::class, 'getBoms'])->middleware('can:manufacturing.boms.view')->name('api.v1.manufacturing.boms.index');
});
