<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechStackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('techstacks')->insert([
            [
                'stack' => 'javascript/typescript',
                'tech' => 'frontend',
                'created_at' => now()
            ],
            [
                'stack' => 'golang',
                'tech' => 'backend',
                'created_at' => now()
            ],
            [
                'stack' => 'next js',
                'tech' => 'frontend',
                'created_at' => now()
            ],
            [
                'stack' => 'laravel',
                'tech' => 'fullstack',
                'created_at' => now()
            ]
        ]);
    }
}
