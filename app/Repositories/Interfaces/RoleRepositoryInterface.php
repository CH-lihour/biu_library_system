<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    public function all();

    public function findById(int $id);

    public function create(array $data, array $permissionIds);

    public function update(int $id, array $data, array $permissionIds);

    public function delete(int $id);

    public function countUsers(int $id): int;
}
