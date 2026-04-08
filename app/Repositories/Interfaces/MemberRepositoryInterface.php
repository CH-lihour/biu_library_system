<?php

namespace App\Repositories\Interfaces;

interface MemberRepositoryInterface
{
    public function createMember(array $data);

    public function updateMember(int $id, array $data);

    public function deleteMember(int $id);

    public function findById(int $id);

    public function getMaxId();

}
