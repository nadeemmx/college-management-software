<?php

namespace App\Models;

class LibraryBook extends BaseModel
{
    public function issues() { return $this->hasMany(LibraryIssue::class); }
}
