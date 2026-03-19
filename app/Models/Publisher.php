<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use SoftDeletes;
    protected $table = "publishers";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    protected $hidded = [
        'deleted_at'
    ];

    public function books(){
        return $this->hasMany(Book::class);
    }
}
