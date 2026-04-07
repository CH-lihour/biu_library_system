<?php

namespace App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {

    }

    public function index(CategoryDataTable $dataTable){
        return $dataTable->render('categories.index');
    }

    public function create(){
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request){
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id){
        $category = $this->categoryService->getCategory($id);
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, int $id){
        $this->categoryService->updateCategory($id, $request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(int $id){
        $this->categoryService->deleteCategory($id);

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
