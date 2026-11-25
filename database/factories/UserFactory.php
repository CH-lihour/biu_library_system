<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => fake()->name(),
            'email'          => fake()->unique()->safeEmail(),
            'is_verified'    => true,
            'password'       => static::$password ??= Hash::make('password'),
            'role_id'        => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * A staff login with the named role (created with no permissions if it is new).
     */
    public function staff(string $role = 'Librarian'): static
    {
        return $this
            ->state(fn () => ['role_id' => Role::firstOrCreate(['name' => $role])->id])
            ->afterCreating(fn (User $user) => $user->staff()->create([
                'firstname' => $user->name,
                'lastname' => '',
                'email' => $user->email,
                'phone' => '',
                'position' => $role,
                'status' => 'active',
            ]));
    }

    /**
     * A member login with a library card.
     */
    public function member(array $card = []): static
    {
        return $this->afterCreating(fn (User $user) => $user->member()->create($card + [
            'member_code' => 'MBR-'.$user->id,
            'name' => $user->name,
            'email' => $user->email,
            'join_date' => today(),
            'expiry_date' => today()->addYear(),
            'status' => 'active',
        ]));
    }

    /**
     * Indicate that the user should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
