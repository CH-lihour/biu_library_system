<?php

namespace App\Repositories;

use App\Models\MemberPlan;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
class MemberRepository implements MemberRepositoryInterface
{
    public function getMaxId()
    {
        return Member::max('id') ?? 0;
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
}
