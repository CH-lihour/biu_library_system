<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberPlan extends Model
{
    use SoftDeletes;

    protected $table = "member_plans";

    protected $fillable = [
        'name',
        'loan_duration_days',
        'fine_per_day',
        'discount_fee',
        'description',
    ];

    protected $casts = [
        'loan_per_days' => 'integer',
        'fine_per_day' => 'decimal',
        'discount_fee' => 'decimal',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function members(){
        return $this->hasMany(Member::class, 'plan_id');
    }
}
