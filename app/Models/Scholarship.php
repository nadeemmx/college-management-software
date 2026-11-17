<?php

namespace App\Models;

class Scholarship extends BaseModel
{
    public function academicSession() { return $this->belongsTo(AcademicSession::class); }
    public function applications() { return $this->hasMany(ScholarshipApplication::class); }
}
