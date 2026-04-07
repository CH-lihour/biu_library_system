<?php

namespace App\Http\Controllers;

use App\DataTables\BookCopyDataTable;
use App\Http\Requests\BookCopy\StoreBookCopyRequest;
use App\Http\Requests\BookCopy\UpdateBookCopyRequest;
use App\Services\BookCopyService;
use Illuminate\Http\Request;
use App\Enums\BookCopyStatus;

class BookCopyController extends Controller
{
    public function __construct(protected BookCopyService $bookCopyService)
    {

    }
    public function index(BookCopyDataTable $dataTable)
    {
        return $dataTable->render('book_copies.index');
    }

    public function create(){
        $books = $this->bookCopyService->getAllBook();
        return view('book_copies.create', compact('books'));
    }

    public function store(StoreBookCopyRequest $request){
        $this->bookCopyService->createBookCopy($request->validated());

        return redirect()->route('book-copies.index')
            ->with('success', 'Book copy created successfully.');
    }

    public function edit($id){
        $bookCopy = $this->bookCopyService->findById($id);
        $books = $this->bookCopyService->getAllBook();
        $statuses = BookCopyStatus::cases();
        return view('book_copies.edit', compact('bookCopy', 'books', 'statuses'));
    }

    public function update(UpdateBookCopyRequest $request, $id)
    {
        $this->bookCopyService->updateBookCopy($id, $request->validated());

        return redirect()->route('book-copies.index')
            ->with('success', 'Book copy updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $this->bookCopyService->deleteBookCopy($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Book copy deleted successfully.']);
        }

        return redirect()->route('book-copies.index')
            ->with('success', 'Book copy deleted successfully.');
    }
}
