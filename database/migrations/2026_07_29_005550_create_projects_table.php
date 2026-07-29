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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Governance', 'Stackholder', 'Budget']);
            $table->text('description');
            $table->foreignId('techstacks_id')
                ->references('id')
                ->on('techstacks');
            $table->string('url');
            $table->string('github');
            $table->string('img'); // thumbnail proyek
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
