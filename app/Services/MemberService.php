<?php

namespace App\Services;

use App\Repositories\MemberRepository;
class MemberService
{
    public function __construct(protected MemberRepository $memberRepository)
    {

    }

    public function getNextMemberCode()
    {
        $memberCodePrefix = 'MBR-';
        $nextNumber = $this->memberRepository->getMaxId() + 1;
        $memberCode = $memberCodePrefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return $memberCode;
    }

    public function findById(int $id)
    {
        return $this->memberRepository->findById($id);
    }

    public function getPlan()
    {
        return $this->memberRepository->getPlan();
    }

    public function createMember(array $data)
    {
        return $this->memberRepository->createMember($data);
    }

    public function updateMember(int $id, array $data)
    {
        return $this->memberRepository->updateMember($id, $data);
    }

    public function deleteMember(int $id)
    {
        return $this->memberRepository->deleteMember($id);
    }
}
