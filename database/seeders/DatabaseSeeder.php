<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::insert([
            [
                'name' => 'admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin12'),
                'role' => 'admin',
            ],
            [
                'name' => 'dona',
                'email' => 'dona@test.com',
                'password' => Hash::make('dona1234'),
                'role' => 'librarian',
            ]
        ]);
    }
}
