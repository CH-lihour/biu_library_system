<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes;
    protected $table = "authors";

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'bio',
    ];

    public function books(){
        return $this->belongsToMany(Book::class, 'book_authors');
    }
}
