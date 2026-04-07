<?php

namespace App\Repositories\Interfaces;

interface PublisherRepositoryInterface
{
    public function findById(int $id);

    public function createPublisher(array $data);

    public function updatePublisher(int $id, array $data);

    public function deletePublisher(int $id);
}
