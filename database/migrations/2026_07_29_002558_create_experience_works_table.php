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
        Schema::create('experience_works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('start_at');
            $table->date('end_at')->nullable(); //nullable karena bisa saja kontrak dia belum berakhir
            $table->enum('position', ['full time', 'part time', 'internship']);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_works');
    }
};
