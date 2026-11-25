<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    /** Creates the login and a membership that staff still have to approve. */
    public function register(array $data): User
    {
        return $this->userRepository->registerMember([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ], [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'join_date' => today(),
            'status' => 'pending',
        ]);
    }
}
