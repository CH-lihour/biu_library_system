<?php

namespace App\Services;

use App\Exceptions\RuleViolation;
use App\Models\User;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
        protected PermissionRepositoryInterface $permissionRepository,
        protected AccessService $access,
    ) {}

    public function all()
    {
        return $this->roleRepository->all();
    }

    /** Roles this user may hand out to a staff account. */
    public function assignableBy(User $actor)
    {
        return $this->all()->filter(
            fn ($role) => $actor->isSuperAdmin()
                || (! $role->is_super && ! array_diff($role->routes(), $actor->role?->routes() ?? []))
        )->values();
    }

    public function findById(int $id)
    {
        return $this->roleRepository->findById($id);
    }

    public function permissions()
    {
        return $this->permissionRepository->grouped();
    }

    public function create(User $actor, array $data)
    {
        $ids = $this->permissionIds($actor, $data);

        return $this->roleRepository->create($this->attributes($data), $ids);
    }

    public function update(User $actor, int $id, array $data)
    {
        $role = $this->findById($id);
        $this->assertEditable($role);
        $this->access->assertCanManageRole($actor, $role);

        return $this->roleRepository->update($id, $this->attributes($data), $this->permissionIds($actor, $data));
    }

    public function delete(User $actor, int $id)
    {
        $role = $this->findById($id);
        $this->assertEditable($role);
        $this->access->assertCanManageRole($actor, $role);

        if ($this->roleRepository->countUsers($id) > 0) {
            throw new RuleViolation('This role is still used by staff accounts. Give them another role first.');
        }

        return $this->roleRepository->delete($id);
    }

    private function assertEditable($role): void
    {
        if ($role->is_super) {
            throw new RuleViolation('The administrator role always has full access and cannot be changed.');
        }
    }

    private function attributes(array $data): array
    {
        return ['name' => $data['name'], 'description' => $data['description'] ?? null];
    }

    private function permissionIds(User $actor, array $data): array
    {
        $permissions = $this->permissionRepository->findMany($data['permissions'] ?? []);
        $this->access->assertHolds($actor, $permissions->pluck('routes')->flatten()->unique()->all());

        return $permissions->modelKeys();
    }
}
