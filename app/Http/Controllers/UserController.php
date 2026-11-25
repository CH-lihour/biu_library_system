<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Http\Requests\User\SaveUserRequest;
use App\Services\RoleService;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService, protected RoleService $roleService)
    {
    }

    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        return view('users.create', ['user' => null, 'roles' => $this->roleService->assignableBy(request()->user())]);
    }

    public function store(SaveUserRequest $request)
    {
        $this->userService->create($request->user(), $request->validated());

        return to_route('users.index')->with('success', 'Staff account created successfully.');
    }

    public function edit(int $user)
    {
        return view('users.edit', [
            'user' => $this->userService->findManageable(request()->user(), $user),
            'roles' => $this->roleService->assignableBy(request()->user()),
        ]);
    }

    public function update(SaveUserRequest $request, int $user)
    {
        $this->userService->update($request->user(), $user, $request->validated());

        return to_route('users.index')->with('success', 'Staff account updated successfully.');
    }

    public function destroy(int $user)
    {
        $this->userService->delete(request()->user(), $user);

        return to_route('users.index')->with('success', 'Staff account deleted successfully.');
    }
}
