<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('bill_items', function (Blueprint $table) {
        $table->unsignedBigInteger('stock_item_id')->nullable()->after('item_name');
        $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('bill_items', function (Blueprint $table) {
        $table->dropForeign(['stock_item_id']);
        $table->dropColumn('stock_item_id');
    });
}
};
