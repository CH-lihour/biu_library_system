<?php

namespace App\Services;


use App\Repositories\CategoryRepository;
class CategoryService
{
    public function __construct(protected CategoryRepository $categoryRepository)
    {

    }

    public function getCategory(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data)
    {
        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory(int $id, array $data)
    {
        return $this->categoryRepository->updateCategory($id, $data);
    }

    public function deleteCategory(int $id)
    {
        return $this->categoryRepository->deleteCategory($id);
    }
}
