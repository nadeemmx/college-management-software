<?php

namespace App\Models;

class LibraryIssue extends BaseModel
{
    public function book() { return $this->belongsTo(LibraryBook::class, "library_book_id"); }
    public function user() { return $this->belongsTo(User::class); }
}
