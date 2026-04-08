<?php

namespace App\Services;

use App\Repositories\MemberPlanRepository;

class MemberPlanService
{
    public function __construct(protected MemberPlanRepository $memberPlanRepository)
    {
    }

    public function findById($id)
    {
        return $this->memberPlanRepository->findById($id);
    }

    public function createMemberPlan(array $data)
    {
        return $this->memberPlanRepository->createMemberPlan($data);
    }

    public function updateMemberPlan($id, array $data)
    {
        return $this->memberPlanRepository->updateMemberPlan($id, $data);
    }

    public function deleteMemberPlan($id)
    {
        return $this->memberPlanRepository->deleteMemberPlan($id);
    }
}
