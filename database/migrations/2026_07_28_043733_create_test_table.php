<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->string('skenario');
            $table->string('kondisi');
            $table->string('ekspektasi');
            $table->string('metode');
            $table->timestamps();
        });

        //test migrate dari container
        DB::connection('supabase')->table('test')->insert([
            'skenario' => 'menambahkan driver pgsql di local dan container',
            'kondisi' => 'menjalankan skenario',
            'ekspektasi' => 'koneksi dapat berjalan',
            'metode' => 'session pooler'
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test');
    }
};
