<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\AuthorDataTable;

class AuthorController extends Controller
{
    public function index(AuthorDataTable $dataTable){
        return $dataTable->render("authors.index");
    }

    public function create(){
        return view("authors.create");
    }
}
