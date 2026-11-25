<?php

namespace App\Services;

use App\Exceptions\RuleViolation;
use App\Models\User;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected RoleRepositoryInterface $roleRepository,
        protected AccessService $access,
    ) {}

    public function findStaff(int $id)
    {
        return $this->userRepository->findStaff($id);
    }

    /** Opening someone's account is already limited to accounts you could manage. */
    public function findManageable(User $actor, int $id)
    {
        $account = $this->findStaff($id);
        $this->access->assertCanManageRole($actor, $account->role);

        return $account;
    }

    public function create(User $actor, array $data)
    {
        $this->access->assertCanManageRole($actor, $this->roleRepository->findById($data['role_id']));

        return $this->userRepository->createStaff(
            $this->account($data) + ['password' => Hash::make($data['password']), 'is_verified' => true],
            $this->profile($data),
        );
    }

    public function update(User $actor, int $id, array $data)
    {
        $account = $this->findManageable($actor, $id);
        $role = $this->roleRepository->findById($data['role_id']);
        $this->access->assertCanManageRole($actor, $role);

        if ($actor->id === $id && ($account->role_id !== $role->id || $data['status'] === 'terminated')) {
            throw new RuleViolation('You cannot change your own role or close your own account.');
        }

        if ($account->role->is_super && (! $role->is_super || $data['status'] === 'terminated')) {
            $this->assertNotLastAdministrator($id);
        }

        $user = $this->account($data);
        if (! empty($data['password'])) {
            $user['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->updateStaff($id, $user, $this->profile($data));
    }

    public function delete(User $actor, int $id)
    {
        $account = $this->findManageable($actor, $id);

        if ($actor->id === $id) {
            throw new RuleViolation('You cannot delete your own account.');
        }

        if ($account->role->is_super) {
            $this->assertNotLastAdministrator($id);
        }

        return $this->userRepository->delete($id);
    }

    private function assertNotLastAdministrator(int $id): void
    {
        if ($this->userRepository->countOtherActiveSuperAdmins($id) === 0) {
            throw new RuleViolation('At least one administrator must remain.');
        }
    }

    private function account(array $data): array
    {
        return [
            'name' => trim($data['firstname'].' '.($data['lastname'] ?? '')),
            'email' => $data['email'],
            'role_id' => (int) $data['role_id'],
        ];
    }

    private function profile(array $data): array
    {
        return [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'position' => $data['position'],
            'status' => $data['status'],
        ];
    }
}
