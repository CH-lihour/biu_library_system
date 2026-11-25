<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->staff('Administrator')->create();
    }

    /** A staff user whose own new role opens exactly these routes. */
    private function staffWith(array $routes, string $role = 'Tester'): User
    {
        $user = User::factory()->staff($role)->create();
        $permission = Permission::create(['name' => "$role access", 'group' => 'Test', 'routes' => $routes]);
        $user->role->permissions()->sync([$permission->id]);

        return $user->fresh();
    }

    private function permissionId(string $name): int
    {
        return Permission::where('name', $name)->firstOrFail()->id;
    }

    public function test_every_permission_points_at_a_real_route(): void
    {
        foreach (Permission::all() as $permission) {
            foreach ($permission->routes as $route) {
                $this->assertTrue(Route::has($route), "$permission->name lists unknown route $route");
            }
        }
    }

    public function test_every_admin_route_is_covered_by_a_permission(): void
    {
        $admin = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('admin.access', $route->gatherMiddleware(), true))
            ->map->getName()
            ->reject(fn ($name) => $name === 'dashboard');

        $this->assertNotEmpty($admin);
        $this->assertSame([], $admin->diff(Permission::all()->pluck('routes')->flatten())->values()->all());
    }

    public function test_administrator_opens_every_admin_list(): void
    {
        $this->actingAs($this->admin());

        foreach (['dashboard', 'books.index', 'members.index', 'borrows.index', 'reservations.index', 'users.index', 'roles.index',
            'users.create', 'roles.create', 'borrows.create'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_list_pages_return_table_data(): void
    {
        $admin = $this->admin();
        User::factory()->member(['status' => 'pending', 'plan_id' => null])->create(['name' => 'Waiting Person']);

        foreach (['roles', 'users', 'reservations', 'members', 'borrows'] as $name) {
            $this->actingAs($admin)
                ->get(route("$name.index", ['draw' => 1, 'start' => 0, 'length' => 10]), ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->assertOk()->assertJsonStructure(['data'])->assertJsonMissingPath('error');
        }

        $this->get(route('members.index', ['draw' => 1]), ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->assertSee('Waiting Person');
    }

    public function test_librarian_keeps_the_library_but_not_administration(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get(route('books.index'))->assertOk();
        $this->get(route('borrows.create'))->assertOk();
        $this->get(route('roles.index'))->assertForbidden();
        $this->get(route('users.create'))->assertForbidden();
        $this->get(route('dashboard'))->assertOk()->assertDontSee(route('roles.index'));
    }

    public function test_admin_creates_a_role_with_its_permissions_in_one_step(): void
    {
        $this->actingAs($this->admin());
        $form = $this->get(route('roles.create'))->assertOk()->getContent();
        foreach (Permission::all() as $permission) {
            $this->assertSame(1, substr_count($form, 'name="permissions[]" value="'.$permission->id.'"'), $permission->name);
        }

        $this->post(route('roles.store'), ['name' => 'Reader', 'permissions' => [$this->permissionId('View authors')]])
            ->assertRedirect(route('roles.index'))->assertSessionHasNoErrors();
        $role = Role::where('name', 'Reader')->firstOrFail();
        $this->get(route('roles.edit', $role->id))->assertOk()->assertSee('Reader');

        $this->post(route('users.store'), [
            'firstname' => 'Sok', 'lastname' => 'Dara', 'email' => 'dara@example.com', 'position' => 'Helper', 'status' => 'active',
            'role_id' => $role->id, 'password' => 'secret-123', 'password_confirmation' => 'secret-123',
        ])->assertRedirect(route('users.index'))->assertSessionHasNoErrors();

        $this->post(route('logout'));
        $this->post(route('store'), ['email' => 'dara@example.com', 'password' => 'secret-123'])->assertRedirect(route('dashboard'));
        $this->get(route('authors.index'))->assertOk()->assertDontSee(route('authors.create'));
        $this->get(route('authors.create'))->assertForbidden();
        $this->get(route('books.index'))->assertForbidden();
    }

    public function test_unknown_permissions_are_refused(): void
    {
        $this->actingAs($this->admin())->post(route('roles.store'), ['name' => 'Odd', 'permissions' => [999999]])->assertSessionHasErrors('permissions.0');
        $this->assertDatabaseMissing('roles', ['name' => 'Odd']);
    }

    public function test_there_is_no_separate_permission_setup(): void
    {
        $this->assertFalse(Route::has('permissions.index'));
        $this->actingAs($this->admin())->get('/permissions')->assertNotFound();
    }

    public function test_removing_a_permission_closes_the_page_at_once(): void
    {
        $user = $this->staffWith(['authors.index']);
        $this->actingAs($user)->get(route('authors.index'))->assertOk();

        $this->actingAs($this->admin())->put(route('roles.update', $user->role_id), ['name' => 'Tester'])->assertSessionHas('success');
        $this->actingAs($user->fresh())->get(route('authors.index'))->assertForbidden();
    }

    public function test_administrator_role_is_protected(): void
    {
        $this->actingAs($this->admin());
        $super = Role::where('is_super', true)->first();

        $this->delete(route('roles.destroy', $super->id))->assertSessionHas('error');
        $this->put(route('roles.update', $super->id), ['name' => 'Weak'])->assertSessionHas('error');
        $this->assertSame('Administrator', $super->fresh()->name);
    }

    public function test_role_in_use_cannot_be_deleted(): void
    {
        $user = $this->staffWith(['authors.index']);
        $this->actingAs($this->admin())->delete(route('roles.destroy', $user->role_id))->assertSessionHas('error');
        $this->assertModelExists($user->role);
    }

    public function test_nobody_can_give_access_they_do_not_have(): void
    {
        $manager = $this->staffWith(['roles.store', 'roles.update', 'users.store', 'users.update', 'users.edit', 'users.destroy', 'books.index'], 'Manager');
        $admin = $this->admin();
        $super = $admin->role;
        $this->actingAs($manager);

        // a role with more rights, for itself or someone else
        $this->post(route('roles.store'), ['name' => 'Boss', 'permissions' => [$this->permissionId('Delete books')]])->assertSessionHas('error');
        $this->put(route('roles.update', $manager->role_id), ['name' => 'Manager', 'permissions' => [$this->permissionId('Delete books')]])->assertSessionHas('error');
        // an administrator account
        $account = ['firstname' => 'A', 'email' => 'a@example.com', 'position' => 'x', 'status' => 'active', 'password' => 'secret-123', 'password_confirmation' => 'secret-123'];
        $this->post(route('users.store'), $account + ['role_id' => $super->id])->assertSessionHas('error');
        // taking over the administrator by changing the password
        $this->get(route('users.edit', $admin->id))->assertSessionHas('error');
        $this->put(route('users.update', $admin->id), $account + ['role_id' => $manager->role_id])->assertSessionHas('error');
        $this->delete(route('users.destroy', $admin->id))->assertSessionHas('error');

        $this->assertDatabaseMissing('roles', ['name' => 'Boss']);
        $this->assertDatabaseMissing('users', ['email' => 'a@example.com']);
        $this->assertNotSoftDeleted($admin);
    }

    public function test_last_administrator_and_own_account_are_safe(): void
    {
        $admin = $this->admin();
        $librarian = Role::where('name', 'Librarian')->first();
        $data = ['firstname' => 'Me', 'email' => $admin->email, 'position' => 'x'];
        $this->actingAs($admin);

        $this->delete(route('users.destroy', $admin->id))->assertSessionHas('error');
        $this->put(route('users.update', $admin->id), $data + ['role_id' => $librarian->id, 'status' => 'active'])->assertSessionHas('error');
        $this->put(route('users.update', $admin->id), $data + ['role_id' => $admin->role_id, 'status' => 'terminated'])->assertSessionHas('error');
        $this->put(route('users.update', $admin->id), $data + ['role_id' => $admin->role_id, 'status' => 'active'])->assertSessionHas('success');

        $this->assertTrue($admin->fresh()->isSuperAdmin());
        $this->assertSame('Me', $admin->fresh()->name);
    }

    public function test_terminated_staff_lose_access_and_cannot_sign_in(): void
    {
        $user = User::factory()->staff()->create();
        $this->actingAs($user)->get(route('books.index'))->assertOk();

        $user->staff->update(['status' => 'terminated']);
        $this->actingAs($user->fresh())->get(route('books.index'))->assertForbidden();

        $this->post(route('logout'));
        $this->post(route('store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
