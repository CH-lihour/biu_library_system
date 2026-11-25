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

    /** Waiting in the queue or ready to collect. */
    public function scopeOpen($query)
    {
        return $query->where('status', 'pending');
    }

    /** A copy is being held for the member right now. */
    public function scopeHeld($query)
    {
        return $query->open()->whereNotNull('notified_at')->where('expires_at', '>', now());
    }

    /** pending, ready, fulfilled, cancelled or expired. */
    public function displayStatus(): string
    {
        if ($this->status !== 'pending' || $this->notified_at === null) {
            return $this->status;
        }

        return $this->expires_at?->isPast() ? 'expired' : 'ready';
    }
}
