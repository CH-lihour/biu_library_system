<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findById(int $id)
    {
        return Category::findOrFail($id);
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function updateCategory(int $id, array $data)
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory(int $id)
    {
        $category = $this->findById($id);
        return $category->delete();
    }
}
