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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('species', 50)->nullable();
            $table->string('breed', 100)->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('arrival_date')->nullable();
            $table->enum('status', ['Available','Adopted','Fostered','Transferred'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
