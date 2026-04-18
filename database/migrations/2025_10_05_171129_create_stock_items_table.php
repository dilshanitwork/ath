<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('model_number')->nullable();
            $table->string('name');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null')->onUpdate('cascade');
            $table->string('color')->nullable();
            $table->string('warranty')->nullable();
            $table->text('other')->nullable();
            $table->tinyInteger('service')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
