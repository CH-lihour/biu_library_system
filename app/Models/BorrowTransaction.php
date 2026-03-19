<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowTransaction extends Model
{
    use SoftDeletes;

    protected $table = "borrow_transactions";

    protected $fillable = [
        'member_id',
        'book_copy_id',
        'staff_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function bookCopy(){
        return $this->belongsTo(BookCopy::class);
    }

    public function staff(){
        return $this->belongsTo(Staff::class);
    }
}
