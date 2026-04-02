<?php

namespace App\Services;

use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $bookRepository
    ) {}

    public function getAllBooks()
    {
        return $this->bookRepository->getAll();
    }

    public function getBook(int $id)
    {
        return $this->bookRepository->findById($id);
    }

    public function createBook(array $data)
    {
        if (isset($data['cover_image_url']) && $data['cover_image_url'] instanceof UploadedFile) {
            $data['cover_image_url'] = $this->uploadImage($data['cover_image_url']);
        }

        return $this->bookRepository->create($data);
    }

    public function updateBook(int $id, array $data)
    {
        $book = $this->bookRepository->findById($id);

        if (isset($data['cover_image_url']) && $data['cover_image_url'] instanceof UploadedFile) {
            if ($book->cover_image_url) {
                Storage::disk('public')->delete($book->cover_image_url);
            }

            $data['cover_image_url'] = $this->uploadImage($data['cover_image_url']);
        }

        return $this->bookRepository->update($id, $data);
    }

    public function deleteBook(int $id)
    {
        return $this->bookRepository->delete($id);
    }

    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('books/covers', 'public');
    }
}
