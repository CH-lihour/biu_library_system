<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\Staff;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function findStaff(int $id)
    {
        return User::with(['staff', 'role.permissions'])->whereNotNull('role_id')->findOrFail($id);
    }

    public function createStaff(array $user, array $staff)
    {
        return DB::transaction(function () use ($user, $staff) {
            $account = User::create($user);
            $account->staff()->create($staff);

            return $account;
        });
    }

    public function updateStaff(int $id, array $user, array $staff)
    {
        return DB::transaction(function () use ($id, $user, $staff) {
            $account = User::findOrFail($id);
            $account->update($user);
            Staff::updateOrCreate(['user_id' => $id], $staff);

            return $account;
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            Staff::where('user_id', $id)->update(['status' => 'terminated']);

            return User::findOrFail($id)->delete();
        });
    }

    public function countOtherActiveSuperAdmins(int $exceptId): int
    {
        return User::whereKeyNot($exceptId)
            ->whereHas('role', fn ($query) => $query->where('is_super', true))
            ->whereDoesntHave('staff', fn ($query) => $query->where('status', 'terminated'))
            ->count();
    }

    public function registerMember(array $user, array $member)
    {
        return DB::transaction(function () use ($user, $member) {
            $account = User::create($user);
            $card = Member::create($member + ['user_id' => $account->id, 'member_code' => 'MBR-NEW']);
            $card->update(['member_code' => 'MBR-'.str_pad((string) $card->id, 5, '0', STR_PAD_LEFT)]);

            return $account;
        });
    }

    public function updateProfile(int $id, array $user, array $member)
    {
        return DB::transaction(function () use ($id, $user, $member) {
            $account = User::findOrFail($id);
            $account->update($user);
            $account->member->update($member);

            return $account;
        });
    }
}
