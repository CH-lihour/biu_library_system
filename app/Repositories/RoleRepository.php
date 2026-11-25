<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleRepositoryInterface
{
    public function all()
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(array $data, array $permissionIds)
    {
        return DB::transaction(function () use ($data, $permissionIds) {
            $role = Role::create($data);
            $role->permissions()->sync($permissionIds);

            return $role;
        });
    }

    public function update(int $id, array $data, array $permissionIds)
    {
        return DB::transaction(function () use ($id, $data, $permissionIds) {
            $role = Role::findOrFail($id);
            $role->update($data);
            $role->permissions()->sync($permissionIds);

            return $role;
        });
    }

    public function delete(int $id)
    {
        return Role::findOrFail($id)->delete();
    }

    public function countUsers(int $id): int
    {
        // Deactivated accounts still point at the role.
        return Role::findOrFail($id)->users()->withTrashed()->count();
    }
}
