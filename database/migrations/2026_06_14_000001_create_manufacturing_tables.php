<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Work Centers
        Schema::create('mfg_work_centers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('name');
            $table->string('code')->index();
            $table->text('description')->nullable();
            $table->decimal('cost_per_hour', 10, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Machines
        Schema::create('mfg_machines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('work_center_id')->nullable()->index();
            $table->string('name');
            $table->string('code')->index();
            $table->decimal('cost_per_hour', 10, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Bills of Materials (BOM)
        Schema::create('mfg_boms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('name');
            $table->string('code')->index();
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. BOM Items (Materials list)
        Schema::create('mfg_bom_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('quantity', 10, 4);
            $table->timestamps();
        });

        // 5. Production Orders
        Schema::create('mfg_production_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('bom_id')->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->string('code')->index();
            $table->decimal('quantity', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('draft'); // draft, planned, in_progress, completed, cancelled
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Work Orders (production steps)
        Schema::create('mfg_work_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id')->index();
            $table->unsignedBigInteger('work_center_id')->index();
            $table->string('name');
            $table->integer('sequence')->default(1);
            $table->decimal('planned_hours', 10, 2)->default(0.00);
            $table->decimal('actual_hours', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamps();
        });

        // 7. Material Consumption details
        Schema::create('mfg_material_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->decimal('qty_expected', 10, 4);
            $table->decimal('qty_consumed', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfg_material_consumptions');
        Schema::dropIfExists('mfg_work_orders');
        Schema::dropIfExists('mfg_production_orders');
        Schema::dropIfExists('mfg_bom_items');
        Schema::dropIfExists('mfg_boms');
        Schema::dropIfExists('mfg_machines');
        Schema::dropIfExists('mfg_work_centers');
    }
};
