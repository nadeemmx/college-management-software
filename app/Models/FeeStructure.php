<?php

namespace App\Models;

class FeeStructure extends BaseModel
{
    public function course() { return $this->belongsTo(Course::class); }
}
