<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $table = "members";

    protected $fillable = [
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
        'phone' => 'integer',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function plan(){
        return $this->belongsTo(MemberPlan::class, 'plan_id');
    }
}
