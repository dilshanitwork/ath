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
        Schema::table('direct_bills', function (Blueprint $table) {
            $table->string('type')->default('cash')->after('contact_number')->comment('cash or credit');

            $table->decimal('paid', 10, 2)->default(0)->after('final_amount')->comment('Total amount paid so far');
            $table->decimal('balance', 10, 2)->default(0)->after('paid')->comment('Remaining amount to pay');

            $table->string('status')->default('closed')->after('balance')->comment('open or closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_bills', function (Blueprint $table) {
            $table->dropColumn(['type', 'paid', 'balance', 'status']);
        });
    }
};
