<?php

namespace App\Repositories;

use App\Models\Publisher;
use App\Repositories\Interfaces\PublisherRepositoryInterface;

class PublisherRepository implements PublisherRepositoryInterface
{
    public function findById(int $id)
    {
        return Publisher::findOrFail($id);
    }

    public function createPublisher(array $data)
    {
        return Publisher::create($data);
    }

    public function updatePublisher(int $id, array $data)
    {
        $publisher = $this->findById($id);
        $publisher->update($data);
        return $publisher;
    }

    public function deletePublisher(int $id)
    {
        $publisher = $this->findById($id);
        $publisher->delete();
    }
}
