<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function findStaff(int $id);

    public function createStaff(array $user, array $staff);

    public function updateStaff(int $id, array $user, array $staff);

    public function delete(int $id);

    public function countOtherActiveSuperAdmins(int $exceptId): int;

    public function registerMember(array $user, array $member);

    public function updateProfile(int $id, array $user, array $member);
}
