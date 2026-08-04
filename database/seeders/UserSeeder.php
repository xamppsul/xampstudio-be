<?php

namespace Database\Seeders;

use App\Infrastructure\Database\Eloquent\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Muhamamd Syamsul Ma'rif",
            'email' => "muhdevapp@gmail.com",
            "password" => Hash::make("Codingweb1129321!@#$")
        ]);
    }
}
