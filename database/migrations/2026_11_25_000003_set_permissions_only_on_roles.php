<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permissions are now a fixed list that is ticked on the role itself, so
     * there are no custom permissions and no screens to manage them.
     */
    public function up(): void
    {
        $this->replaceCustomPermissions();

        DB::table('permissions')->where('is_system', false)->delete();
        DB::table('permissions')->where('group', 'Permissions')->delete();

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('routes');
        });

        DB::table('permissions')->update(['is_system' => true]);

        foreach (['View' => ['index'], 'Add' => ['create', 'store'], 'Edit' => ['edit', 'update'], 'Delete' => ['destroy']] as $verb => $actions) {
            DB::table('permissions')->insert([
                'name' => "$verb permissions",
                'group' => 'Permissions',
                'routes' => json_encode(array_map(fn ($action) => "permissions.$action", $actions)),
                'is_system' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    /**
     * Roles that used a custom permission get the built-in ones that fit fully
     * inside the access they already had, so nobody gains anything.
     */
    private function replaceCustomPermissions(): void
    {
        $permissions = DB::table('permissions')->get()->keyBy('id');
        $routes = fn ($id) => json_decode($permissions[$id]->routes, true);

        $roleIds = DB::table('permission_role')
            ->whereIn('permission_id', $permissions->where('is_system', false)->keys())
            ->distinct()->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $held = DB::table('permission_role')->where('role_id', $roleId)->pluck('permission_id');
            $allowed = $held->flatMap($routes)->unique()->all();

            foreach ($permissions->where('is_system', true)->keys()->diff($held) as $id) {
                if (! array_diff($routes($id), $allowed)) {
                    DB::table('permission_role')->insert(['permission_id' => $id, 'role_id' => $roleId]);
                }
            }
        }
    }
};
