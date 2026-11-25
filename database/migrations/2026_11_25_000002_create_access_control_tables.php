<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Roles become data instead of a fixed enum. A permission lists the named
     * routes it opens, so both can be created from the admin panel.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_super')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('group', 50);
            $table->json('routes');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->restrictOnDelete();
        });

        $this->seedDefaults();
        $this->moveUsersToRoles();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Custom roles have no place in the old enum, so their users fall back to
     * the least powerful staff role.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'librarian', 'member'])->default('member')->after('password');
        });

        $super = DB::table('roles')->where('is_super', true)->pluck('id');

        DB::table('users')->whereNotNull('role_id')->update(['role' => 'librarian']);
        DB::table('users')->whereIn('role_id', $super)->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }

    private function seedDefaults(): void
    {
        $now = now();

        DB::table('roles')->insert([
            'name' => 'Administrator',
            'description' => 'Full access to everything.',
            'is_super' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $librarian = DB::table('roles')->insertGetId([
            'name' => 'Librarian',
            'description' => 'Catalogue, members and the borrow desk.',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        foreach ($this->defaultPermissions() as [$group, $name, $routes, $forLibrarian]) {
            $id = DB::table('permissions')->insertGetId([
                'name' => $name,
                'group' => $group,
                'routes' => json_encode($routes),
                'is_system' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            if ($forLibrarian) {
                DB::table('permission_role')->insert(['permission_id' => $id, 'role_id' => $librarian]);
            }
        }
    }

    /**
     * @return array<int, array{string, string, array<int, string>, bool}>
     */
    private function defaultPermissions(): array
    {
        $resources = [
            ['Books', 'books', 'books', true],
            ['Book copies', 'book copies', 'book-copies', true],
            ['Authors', 'authors', 'authors', true],
            ['Publishers', 'publishers', 'publishers', true],
            ['Categories', 'categories', 'categories', true],
            ['Members', 'members', 'members', true],
            ['Member plans', 'member plans', 'member-plans', true],
            ['Staff accounts', 'staff accounts', 'users', false],
            ['Roles', 'roles', 'roles', false],
            ['Permissions', 'permissions', 'permissions', false],
        ];

        $permissions = [];
        foreach ($resources as [$group, $label, $route, $forLibrarian]) {
            $hasDetails = ! in_array($route, ['users', 'roles', 'permissions'], true);
            $permissions[] = [$group, "View $label", $hasDetails ? ["$route.index", "$route.show"] : ["$route.index"], $forLibrarian];
            $permissions[] = [$group, "Add $label", ["$route.create", "$route.store"], $forLibrarian];
            $permissions[] = [$group, "Edit $label", ["$route.edit", "$route.update"], $forLibrarian];
            $permissions[] = [$group, "Delete $label", ["$route.destroy"], $forLibrarian];
        }

        return array_merge($permissions, [
            ['Members', 'Approve registrations', ['members.approval', 'members.approve', 'members.reject'], true],
            ['Borrow desk', 'View loans', ['borrows.index'], true],
            ['Borrow desk', 'Lend books', ['borrows.create', 'borrows.store', 'borrows.getBookByBarcode'], true],
            ['Borrow desk', 'Receive returns', ['borrows.return'], true],
            ['Reservations', 'View reservations', ['reservations.index'], true],
            ['Reservations', 'Manage reservations', ['reservations.ready', 'reservations.cancel'], true],
        ]);
    }

    private function moveUsersToRoles(): void
    {
        $map = [
            'admin' => DB::table('roles')->where('name', 'Administrator')->value('id'),
            'librarian' => DB::table('roles')->where('name', 'Librarian')->value('id'),
        ];

        foreach ($map as $old => $roleId) {
            DB::table('users')->where('role', $old)->update(['role_id' => $roleId]);
        }

        // Loans record the staff row that handled them, so every staff login needs one.
        foreach (DB::table('users')->whereIn('role', array_keys($map))->get() as $user) {
            if (DB::table('staff')->where('user_id', $user->id)->orWhere('email', $user->email)->exists()) {
                continue;
            }

            DB::table('staff')->insert([
                'user_id' => $user->id,
                'firstname' => $user->name,
                'lastname' => '',
                'email' => mb_substr($user->email, 0, 100),
                'phone' => '',
                'position' => ucfirst($user->role),
                'status' => $user->deleted_at ? 'terminated' : 'active',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
};
