<?php

namespace App\Repositories;

use App\Models\MemberPlan;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class MemberRepository implements MemberRepositoryInterface
{
    public function getMaxId()
    {
        return Member::withTrashed()->max('id') ?? 0;
    }

    public function findById(int $id)
    {
        return Member::findOrFail($id);
    }

    public function getPlan(){
        return MemberPlan::all();
    }

    public function createMember(array $data)
    {
        return Member::create($data);
    }

    public function updateMember(int $id, array $data)
    {
        $member = $this->findById($id);
        $member->update($data);

        return $member;
    }

    public function deleteMember(int $id)
    {
        $member = $this->findById($id);
        $member->delete();

        return $member;
    }

    public function unlinkedCardsByEmail(string $email)
    {
        return Member::whereNull('user_id')->where('email', $email)->get();
    }

    /** Gives the older card the login of the newer registration, which then has no purpose. */
    public function moveLogin(Member $from, Member $to): void
    {
        DB::transaction(function () use ($from, $to) {
            $userId = $from->user_id;
            $from->update(['user_id' => null]);
            $from->forceDelete();
            $to->update(['user_id' => $userId]);
        });
    }
}
