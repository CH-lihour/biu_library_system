<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCopy extends Model
{
    use SoftDeletes;
    protected $table = "book_copies";

    protected $fillable = [
        'book_id',
        'barcode',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function book(){
        return $this->belongsTo(Book::class);
    }

    public function borrows(){
        return $this->hasMany(BorrowTransaction::class, 'copy_id');
    }
}
