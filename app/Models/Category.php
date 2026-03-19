<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = "categories";

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function books(){
        return $this->hasMany(Book::class);
    }
}
