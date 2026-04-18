<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->string('cheque_number', 100);
            $table->string('bank', 150);
            $table->decimal('amount', 12, 2);
            $table->date('cheque_date');

            $table->enum('status', ['pending', 'settled', 'cancelled'])->default('pending');
            $table->text('note')->nullable();

            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->index(['customer_id', 'status', 'cheque_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
