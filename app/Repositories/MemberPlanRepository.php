<?php

namespace App\Repositories;

use App\Models\MemberPlan;
use App\Repositories\Interfaces\MemberPlanRepositoryInterface;

class MemberPlanRepository implements MemberPlanRepositoryInterface
{
    public function findById($id)
    {
        return MemberPlan::find($id);
    }

    public function createMemberPlan(array $data)
    {
        return MemberPlan::create($data);
    }

    public function updateMemberPlan($id, array $data)
    {
        $memberPlan = $this->findById($id);
        $memberPlan->update($data);
        return $memberPlan;
    }

    public function deleteMemberPlan($id)
    {
        $memberPlan = $this->findById($id);
        $memberPlan->delete();
    }
}
