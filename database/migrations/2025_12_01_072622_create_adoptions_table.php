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
        Schema::create('adoptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')
                  ->nullable()
                  ->constrained('animals')
                  ->onDelete('cascade');
            $table->date('adoption_date')->nullable();
            $table->string('adopter_name', 100)->nullable();
            $table->string('adopter_phone', 20)->nullable();
            $table->string('adopter_email', 100)->nullable();
            $table->text('adopter_address')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoptions');
    }
};
