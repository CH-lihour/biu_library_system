<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    protected $table = "books";

    protected $fillable = [
        'isbn',
        'title',
        'publisher_id',
        'category_id',
        'publish_year',
        'pages',
        'language',
        'cover_image_url',
        'shelf_location',
    ];

    protected $casts = [
        'pages'        => 'integer',
        'publish_year' => 'integer',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function publisher(){
        return $this->belongsTo(Publisher::class);
    }

    public function authors(){
        return $this->belongsToMany(Author::class, 'book_authors');
    }

    public function copies(){
        return $this->hasMany(BookCopy::class);
    }
}
