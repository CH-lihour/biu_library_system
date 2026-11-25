<?php

namespace App\Repositories\Interfaces;

interface PermissionRepositoryInterface
{
    public function grouped();

    public function findMany(array $ids);

    public function all();

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}
