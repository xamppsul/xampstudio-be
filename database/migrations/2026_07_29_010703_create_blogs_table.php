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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('techstacks_id')
                ->references('id')
                ->on('techstacks');
            $table->string('title');
            $table->string('permalink');
            $table->boolean('is_read');
            $table->time('reading_time');
            $table->string('author');
            $table->date('date_post');
            $table->text('content');
            $table->string('img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
