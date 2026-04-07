<?php

namespace App\Services;

use App\Repositories\PublisherRepository;
class PublisherService
{
    public function __construct(protected PublisherRepository $publisherRepository)
    {

    }

    public function getPublisher(int $id)
    {
        return $this->publisherRepository->findById($id);
    }

    public function createPublisher(array $data)
    {
        return $this->publisherRepository->createPublisher($data);
    }

    public function updatePublisher(int $id, array $data)
    {
        return $this->publisherRepository->updatePublisher($id, $data);
    }

    public function deletePublisher(int $id)
    {
        return $this->publisherRepository->deletePublisher($id);
    }
}
