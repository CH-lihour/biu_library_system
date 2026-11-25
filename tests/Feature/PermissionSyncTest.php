<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PermissionSyncTest extends TestCase
{
    use RefreshDatabase;

    private function adminRoute(string $name): void
    {
        Route::middleware(['web', 'auth', 'admin.access'])->get('/test/'.str_replace('.', '/', $name), fn () => 'page '.$name)->name($name);
        Route::getRoutes()->refreshNameLookups();
    }

    private function sync(): array
    {
        return app(PermissionSyncService::class)->sync();
    }

    private function snapshot(): array
    {
        return Permission::orderBy('id')->get()->map->only(['name', 'group', 'routes'])->all();
    }

    public function test_it_changes_nothing_when_the_routes_did_not_change(): void
    {
        $before = $this->snapshot();

        $this->assertSame(['removed' => [], 'added' => []], $this->sync());
        $this->assertSame($before, $this->snapshot());
        $this->assertCount(42, $before);
    }

    public function test_a_new_module_gets_its_own_group_and_goes_to_no_role(): void
    {
        foreach (['reports.index', 'reports.show', 'reports.create', 'reports.store', 'reports.edit', 'reports.update', 'reports.destroy', 'reports.export-pdf'] as $name) {
            $this->adminRoute($name);
        }

        $this->sync();

        $this->assertSame([
            'View reports' => ['reports.index', 'reports.show'],
            'Add reports' => ['reports.create', 'reports.store'],
            'Edit reports' => ['reports.edit', 'reports.update'],
            'Delete reports' => ['reports.destroy'],
            'Export pdf reports' => ['reports.export-pdf'],
        ], Permission::where('group', 'Reports')->orderBy('id')->pluck('routes', 'name')->all());
        $this->assertSame(0, Permission::where('group', 'Reports')->whereHas('roles')->count());
    }

    public function test_a_new_action_joins_the_existing_group_without_widening_a_permission(): void
    {
        $this->adminRoute('users.show');
        $this->adminRoute('books.printLabel');
        $routes = Permission::where('name', 'View staff accounts')->value('routes');

        $this->assertSame(['View staff accounts (show)', 'Print label books'], $this->sync()['added']);

        $this->assertSame($routes, Permission::where('name', 'View staff accounts')->value('routes'));
        $this->assertSame('Staff accounts', Permission::where('name', 'View staff accounts (show)')->value('group'));
        $this->assertSame('Books', Permission::where('name', 'Print label books')->value('group'));
        $this->assertSame(34, Role::where('name', 'Librarian')->first()->permissions()->count());
    }

    public function test_migrate_creates_the_permission_and_ticking_it_opens_the_page(): void
    {
        $this->adminRoute('reports.index');
        $this->artisan('migrate')->assertSuccessful();
        $permission = Permission::where('name', 'View reports')->firstOrFail();

        $admin = User::factory()->staff('Administrator')->create();
        $librarian = User::factory()->staff()->create();
        $this->actingAs($librarian)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $this->get(route('roles.create'))->assertSee('View reports');

        $role = $librarian->role;
        $this->put(route('roles.update', $role->id), [
            'name' => $role->name,
            'permissions' => [...$role->permissions->modelKeys(), $permission->id],
        ])->assertSessionHas('success');

        $this->actingAs($librarian->fresh())->get(route('reports.index'))->assertOk()->assertSee('page reports.index');
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $this->adminRoute('reports.index');
        event(new MigrationsEnded('up', ['pretend' => true]));
        event(new MigrationsEnded('down', []));

        $this->assertDatabaseMissing('permissions', ['name' => 'View reports']);
    }

    public function test_permissions_of_removed_routes_are_cleaned_up(): void
    {
        $gone = Permission::create(['name' => 'Old thing', 'group' => 'Old', 'routes' => ['old.index']]);
        $partly = Permission::where('name', 'View books')->first();
        $partly->update(['routes' => ['books.index', 'books.old', 'books.show']]);

        $this->assertSame(['Old thing'], $this->sync()['removed']);
        $this->assertModelMissing($gone);
        $this->assertSame(['books.index', 'books.show'], $partly->fresh()->routes);
    }

    public function test_nothing_is_deleted_when_no_routes_are_loaded(): void
    {
        Route::setRoutes(new RouteCollection);

        $this->sync();
        $this->assertSame(42, Permission::count());
    }

    public function test_portal_and_public_routes_never_become_permissions(): void
    {
        $this->assertSame(0, Permission::all()->pluck('routes')->flatten()
            ->filter(fn ($name) => str_starts_with($name, 'portal.') || in_array($name, ['login', 'register', 'logout', 'dashboard'], true))
            ->count());
    }

    public function test_the_command_reports_what_it_did(): void
    {
        $this->adminRoute('reports.index');

        $this->artisan('permissions:sync')->expectsOutputToContain('Added: View reports')->expectsOutputToContain('Removed: nothing')->assertSuccessful();
    }
}
