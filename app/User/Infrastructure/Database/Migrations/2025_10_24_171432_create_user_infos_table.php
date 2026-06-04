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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->date('birthday')->nullable();
            $table->tinyInteger('gender')->nullable(); // 1 => male, 2 => female
            $table->tinyInteger('fitness_level')->nullable();  // 1 =>  beginner, 2 => intermediate, 3 => advanced
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
