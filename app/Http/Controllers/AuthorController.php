<?php

namespace App\Http\Controllers;

use App\DataTables\AuthorDataTable;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Services\AuthorService;

class AuthorController extends Controller
{

    public function __construct(protected AuthorService $authorService)
    {

    }
    public function index(AuthorDataTable $dataTable){
        return $dataTable->render("authors.index");
    }

    public function create(){
        return view("authors.create");
    }

    public function store(StoreAuthorRequest $request){
        $this->authorService->createAuthor($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author created successfully.');
    }

    public function edit($id){
        $author = $this->authorService->getAuthor($id);
        return view("authors.edit", compact('author'));
    }

    public function update(UpdateAuthorRequest $request, $id){
        $this->authorService->updateAuthor($id, $request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author updated successfully.');
    }

    public function destroy($id){
        $this->authorService->deleteAuthor($id);

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}
