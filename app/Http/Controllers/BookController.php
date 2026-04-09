<?php

namespace App\Http\Controllers;

use App\DataTables\BookDataTable;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Author;
use App\Services\BookService;
use App\Models\Category;
use App\Models\Publisher;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {
    }

    public function index(BookDataTable $dataTable)
    {
        return $dataTable->render('books.index');
    }

    public function create()
    {
        $categories = Category::all();
        $publishers = Publisher::all();
        $authors = Author::all();
        return view('books.create', compact('categories', 'publishers', 'authors'));
    }

    public function store(StoreBookRequest $request)
    {
        $this->bookService->createBook($request->validated());
        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function edit(int $id)
    {
        $book = $this->bookService->getBook($id);
        
        $categories = Category::all();
        $publishers = Publisher::all();
        $authors    = Author::all();

        return view('books.edit', compact('book', 'categories', 'publishers', 'authors'));
    }

    public function update(UpdateBookRequest $request, int $id)
    {
        $this->bookService->updateBook($id, $request->validated());
        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->bookService->deleteBook($id);
        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
