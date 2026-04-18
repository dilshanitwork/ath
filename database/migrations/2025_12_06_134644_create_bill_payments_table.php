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
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_bill_id')->constrained('direct_bills')->onDelete('cascade');
            
            // Payment details
            $table->decimal('amount', 10, 2); // Amount paid in this specific transaction
            $table->date('paid_date');
            $table->string('payment_method')->default('cash'); // cash, bank transfer, cheque etc.
            $table->text('note')->nullable(); // Optional note for this payment
            
            // Who recorded this payment
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
