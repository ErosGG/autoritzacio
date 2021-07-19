<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "nick" => "eros",
            "name" => "Eros Gonzàlez",
            "email" => "gonzalez.eros@gmail.com",
            "email_verified_at" => now(),
            "password" => Hash::make("12345678"),
            "remember_token" => Str::random(10),
        ]);

        User::factory()
            ->times(9)
            ->create();
    }
}
