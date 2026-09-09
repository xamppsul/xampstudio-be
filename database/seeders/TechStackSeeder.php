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
                'tech' => 'javascript/typescript',
                'stack' => 'frontend',
                'created_at' => now()
            ],
            [
                'tech' => 'golang',
                'stack' => 'backend',
                'created_at' => now()
            ],
            [
                'tech' => 'next js',
                'stack' => 'frontend',
                'created_at' => now()
            ],
            [
                'tech' => 'laravel',
                'stack' => 'fullstack',
                'created_at' => now()
            ]
        ]);
    }
}
