<?php

namespace App\Repositories\Interfaces;

interface MemberPlanRepositoryInterface
{
    public function findById($id);

    public function createMemberPlan(array $data);

    public function updateMemberPlan($id, array $data);

    public function deleteMemberPlan($id);
}
