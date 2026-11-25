<?php

namespace App\Services;

use App\Exceptions\RuleViolation;
use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
class MemberService
{
    public function __construct(protected MemberRepositoryInterface $memberRepository)
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

    public function findPending(int $id): Member
    {
        $member = $this->findById($id);

        if (! $member->isPending()) {
            throw new RuleViolation('This registration was already handled.');
        }

        return $member;
    }

    /** Cards staff made earlier for the same email, which the new login may belong to. */
    public function cardsMatching(Member $member)
    {
        return $this->memberRepository->unlinkedCardsByEmail((string) $member->email);
    }

    public function approve(int $id, array $data): void
    {
        $member = $this->findPending($id);

        if (empty($data['link_member_id'])) {
            $this->memberRepository->updateMember($id, [
                'plan_id' => $data['plan_id'],
                'expiry_date' => $data['expiry_date'],
                'join_date' => today(),
                'status' => 'active',
            ]);

            return;
        }

        $card = $this->cardsMatching($member)->firstWhere('id', (int) $data['link_member_id']);

        if (! $card) {
            throw new RuleViolation('That card cannot be linked to this registration.');
        }

        $this->memberRepository->moveLogin($member, $card);
    }

    public function reject(int $id): void
    {
        $this->memberRepository->updateMember($this->findPending($id)->id, ['status' => 'inactive']);
    }
}
