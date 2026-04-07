<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function findById(int $id);

    public function createCategory(array $data);

    public function updateCategory(int $id, array $data);

    public function deleteCategory(int $id);
}
