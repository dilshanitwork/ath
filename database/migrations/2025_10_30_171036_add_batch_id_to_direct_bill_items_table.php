<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('direct_bill_items', function (Blueprint $table) {
            // Add batch_id column to track which specific batch this item came from
            $table->unsignedBigInteger('batch_id')->nullable()->after('stock_item_id');
            
            // Add foreign key constraint
            $table->foreign('batch_id')
                  ->references('id')
                  ->on('stock_batches')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('direct_bill_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};