<?php

namespace App\Models;

class Certificate extends BaseModel
{
    public function student() { return $this->belongsTo(Student::class); }
}
