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
        Schema::create('direct_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_bill_id')->constrained('direct_bills')->onDelete('cascade');
            $table->foreignId('stock_item_id')->nullable()->constrained('stock_items')->onDelete('set null');
            $table->string('item_name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_bill_items');
    }
};
