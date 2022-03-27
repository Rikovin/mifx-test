<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookAuthor extends Model
{

    protected $table = 'book_author';
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'author_id'
    ];
}
