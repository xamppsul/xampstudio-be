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
        Schema::create('experience_work_techstacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_works_id')
                ->references('id')
                ->on('experience_works');
            $table->foreignId('techstacks_id')
                ->references('id')
                ->on('techstacks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_work_techstacks');
    }
};
