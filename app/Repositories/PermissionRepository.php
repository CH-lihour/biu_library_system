<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function grouped()
    {
        return Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
    }

    public function findMany(array $ids)
    {
        return Permission::whereIn('id', $ids)->get();
    }

    public function all()
    {
        return Permission::orderBy('id')->get();
    }

    public function create(array $data)
    {
        return Permission::create($data);
    }

    public function update(int $id, array $data)
    {
        return Permission::findOrFail($id)->update($data);
    }

    public function delete(int $id)
    {
        return Permission::findOrFail($id)->delete();
    }
}
