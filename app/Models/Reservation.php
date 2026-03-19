<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $table = "reservations";

    protected $fillable = [
        'member_id',
        'book_id',
        'status',
        'expires_at',
        'notified_at',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function book(){
        return $this->belongsTo(Book::class);
    }
}
