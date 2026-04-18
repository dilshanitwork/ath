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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('category')->nullable();
            $table->string('address');
            $table->string('email')->nullable()->unique();
            $table->string('mobile');
            $table->string('mobile_2')->nullable();
            $table->string('nic')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->integer('hometown')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('credit_limit', 10, 2);
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
