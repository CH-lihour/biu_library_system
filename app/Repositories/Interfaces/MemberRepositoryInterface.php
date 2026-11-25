<?php

namespace App\Repositories\Interfaces;

use App\Models\Member;

interface MemberRepositoryInterface
{
    public function createMember(array $data);

    public function updateMember(int $id, array $data);

    public function deleteMember(int $id);

    public function findById(int $id);

    public function getMaxId();

    public function unlinkedCardsByEmail(string $email);

    public function moveLogin(Member $from, Member $to): void;
}
