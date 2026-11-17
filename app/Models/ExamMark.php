<?php

namespace App\Models;

class ExamMark extends BaseModel
{
    public function exam() { return $this->belongsTo(Exam::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function student() { return $this->belongsTo(Student::class); }
}
