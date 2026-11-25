<?php

namespace Database\Seeders;

use App\Models\Role;
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

        // The roles come from the access control migration.
        $staff = [
            ['admin', 'admin@test.com', 'admin12', 'Administrator'],
            ['dona', 'dona@test.com', 'dona1234', 'Librarian'],
        ];

        foreach ($staff as [$name, $email, $password, $role]) {
            User::create([
                'name'      => $name,
                'email'     => $email,
                'password'  => Hash::make($password),
                'role_id'   => Role::where('name', $role)->value('id'),
            ])->staff()->create([
                'firstname' => $name,
                'lastname'  => '',
                'email'     => $email,
                'phone'     => '',
                'position'  => $role,
                'status'    => 'active',
            ]);
        }

        $member = User::create([
            'name'     => 'pich',
            'email'    => 'pich@test.com',
            'password' => Hash::make('pich1234'),
        ])->member()->create([
            'member_code' => 'MBR-NEW',
            'name'        => 'pich',
            'email'       => 'pich@test.com',
            'join_date'   => today(),
            'expiry_date' => today()->addYear(),
            'status'      => 'active',
        ]);
        $member->update(['member_code' => 'MBR-'.str_pad((string) $member->id, 5, '0', STR_PAD_LEFT)]);
    }
}
