<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 255);
            $table->string('contact_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
