<?php

namespace App\Http\Controllers;

use App\DataTables\BorrowTransactionDataTable;
use App\Http\Requests\BookTransaction\StoreBorrowRequest;
use App\Services\BorrowService;

class BorrowTransactionController extends Controller
{

    public function __construct(protected BorrowService $borrowService)
    {

    }
    public function index(BorrowTransactionDataTable $dataTable)
    {
        return $dataTable->render('borrows.index');
    }

    public function create()
    {
        $members = $this->borrowService->getActiveMembers();
        return view('borrows.create', compact('members'));
    }

    public function store(StoreBorrowRequest $request){
        $validatedData = $request->validated();
        $this->borrowService->createBorrowTransaction($validatedData);

        return to_route('borrows.index')->with('success', 'Book borrowed successfully.');
    }

    public function getBookByBarcode()
    {
        $barcode = request()->input('barcode');
        $bookCopy = $this->borrowService->getBookByBarcode($barcode);

        if ($bookCopy) {
            return response()->json([
                'success' => true,
                'book' => [
                    'book_copy_id' => $bookCopy->id,
                    'title' => $bookCopy->book->title,
                    'isbn' => $bookCopy->book->isbn,
                    'publish_year' => $bookCopy->book->publish_year,
                    'language' => $bookCopy->book->language,
                    'pages' => $bookCopy->book->pages,
                    'shelf_location' => $bookCopy->book->shelf_location,
                    'cover_image_url' => $bookCopy->book->cover_image_url,
                    'publisher' => optional($bookCopy->book->publisher)->name,
                    'category' => optional($bookCopy->book->category)->name,
                    'authors' => $bookCopy->book->authors->pluck('name')->implode(', '),
                    'barcode' => $bookCopy->barcode,
                    'status' => $bookCopy->status,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Book not found or not available'
        ]);
    }
}
