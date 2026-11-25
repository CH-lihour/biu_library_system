<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $table = "members";

    protected $fillable = [
        'user_id',
        'plan_id',
        'member_code',
        'name',
        'email',
        'join_date',
        'expiry_date',
        'address',
        'phone',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function plan(){
        return $this->belongsTo(MemberPlan::class, 'plan_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function loans(){
        return $this->hasMany(BorrowTransaction::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** BR-04: why this member may not borrow or reserve, or null when they may. */
    public function blockedReason(): ?string
    {
        return match (true) {
            $this->status === 'pending' => 'This membership is waiting for approval.',
            $this->status !== 'active' => 'This membership is not active.',
            $this->expiry_date !== null && $this->expiry_date->lt(today()) => 'This membership has expired.',
            default => null,
        };
    }
}
