<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('admin/manufacturing')->name('admin.manufacturing.')->group(function () {
    Route::get('/work-centers', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\WorkCenters::class)->name('work-centers.index');
    Route::get('/boms', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\Boms::class)->name('boms.index');
    Route::get('/production-orders', \Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing\ProductionOrders::class)->name('production-orders.index');
});
