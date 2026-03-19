<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;
    protected $table = "notifications";

    protected $fillable = [
        'member_id',
        'title',
        'body',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }
}
