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
        Schema::create('tyre_repairs', function (Blueprint $table) {
            $table->id();
            // Item number will be auto-generated starting from 9000 via Model Event
            $table->unsignedBigInteger('item_number')->unique();

            // Customer is mandatory
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            $table->date('received_date')->nullable();
            $table->string('tyre_size')->nullable();
            $table->string('tyre_make')->nullable();
            $table->string('tyre_number')->nullable();
            $table->date('sent_date')->nullable();
            $table->string('rep_receipt_number')->nullable();
            $table->string('job_number')->nullable();
            $table->date('received_from_company_date')->nullable();
            $table->date('issued_date')->nullable();
            $table->string('bill_number')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = received, 1 = sent to company, 2 = received from company, 3 = issued back to customer, 4 = rejected

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tyre_repairs');
    }
};
