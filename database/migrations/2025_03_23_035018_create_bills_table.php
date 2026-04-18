<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->tinyInteger('category')->nullable();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->decimal('advance_payment', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->timestamps();
            $table->tinyInteger('type')->nullable();
            $table->date('next_bill')->nullable();
            $table->decimal('next_payment', 10, 2)->nullable();
            $table->decimal('installment_payment', 10, 2)->default(0.0);
            $table->integer('installments')->nullable();
            $table->enum('payment_type', ['cash', 'card', 'online'])->default('cash');
            $table->text('guarantor_name')->nullable();
            $table->text('guarantor_mobile')->nullable();
            $table->text('guarantor_nic')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
