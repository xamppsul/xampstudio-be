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
        Schema::create('experience_work_achivements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_works_id')
                ->references('id')
                ->on('experience_works');
            $table->string('achive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_work_achivements');
    }
};
