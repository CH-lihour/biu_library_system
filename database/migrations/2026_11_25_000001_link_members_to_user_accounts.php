<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A member can own a login (users row), and a self-registered member waits
     * as "pending" until staff confirm the membership.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'inactive', 'suspend', 'expired'])->default('active')->change();
        });

        $this->linkExistingMemberLogins();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('members')->where('status', 'pending')->update(['status' => 'inactive']);

        Schema::table('members', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'suspend', 'expired'])->default('active')->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Logins that already have the member role keep working: they are linked to
     * the card with the same email, or get a pending card of their own.
     */
    private function linkExistingMemberLogins(): void
    {
        foreach (DB::table('users')->where('role', 'member')->get() as $user) {
            $cards = DB::table('members')->whereNull('user_id')->whereNull('deleted_at')->where('email', $user->email)->pluck('id');

            if ($cards->count() === 1) {
                DB::table('members')->where('id', $cards->first())->update(['user_id' => $user->id]);

                continue;
            }

            $id = DB::table('members')->insertGetId([
                'user_id' => $user->id,
                'member_code' => 'MBR-NEW',
                'name' => $user->name,
                'email' => mb_substr($user->email, 0, 100),
                'join_date' => now()->toDateString(),
                'status' => 'pending',
                'deleted_at' => $user->deleted_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('members')->where('id', $id)->update(['member_code' => 'MBR-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT)]);
        }
    }
};
