<?php

namespace App\Http\Controllers;

use App\DataTables\RoleDataTable;
use App\Http\Requests\Role\SaveRoleRequest;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}

    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        return view('roles.create', ['role' => null, 'groups' => $this->roleService->permissions()]);
    }

    public function store(SaveRoleRequest $request)
    {
        $this->roleService->create($request->user(), $request->validated());

        return to_route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(int $role)
    {
        $role = $this->roleService->findById($role);

        return view('roles.edit', ['role' => $role, 'groups' => $this->roleService->permissions()]);
    }

    public function update(SaveRoleRequest $request, int $role)
    {
        $this->roleService->update($request->user(), $role, $request->validated());

        return to_route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(int $role)
    {
        $this->roleService->delete(request()->user(), $role);

        return to_route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
