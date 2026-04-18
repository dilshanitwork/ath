<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->string('uc_number', 100); 
            $table->string('tire_size', 100);
            $table->string('tyre_serial_number', 150);

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('company_id');

            $table->date('customer_given_date')->nullable();
            $table->date('company_sent_date')->nullable();
            $table->date('company_received_date')->nullable();
            $table->date('customer_hand_over_date')->nullable();

            $table->decimal('amount_to_customer', 12, 2)->default(0);

            $table->enum('status', ['claimed_100', 'half_claim', 'rejected'])->default('claimed_100');

            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');

            $table->index(['uc_number', 'status']);
            $table->index(['customer_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
