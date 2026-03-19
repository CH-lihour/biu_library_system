<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $table = "reviews";

    protected $fillable = [
        'member_id',
        'book_id',
        'rating',
        'review_text',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function book(){
        return $this->belongsTo(Book::class);
    }
}
