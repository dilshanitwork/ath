<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
           
            $table->unsignedBigInteger('bank_value_id')->nullable()->after('cheque_number');

            $table->foreign('bank_value_id')
                ->references('id')
                ->on('attribute_values')
                ->onDelete('restrict');

            $table->index('bank_value_id');
        });

       

        Schema::table('cheques', function (Blueprint $table) {
            // drop old bank column after new column exists
            if (Schema::hasColumn('cheques', 'bank')) {
                $table->dropColumn('bank');
            }
        });

        
    }

    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
           
            $table->string('bank', 150)->nullable()->after('cheque_number');

            
            $table->dropForeign(['bank_value_id']);
            $table->dropIndex(['bank_value_id']);
            $table->dropColumn('bank_value_id');
        });
    }
};
