<?php

namespace App\Models;

class Exam extends BaseModel
{
    public function academicSession() { return $this->belongsTo(AcademicSession::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function schedules() { return $this->hasMany(ExamSchedule::class); }
    public function registrations() { return $this->hasMany(ExamRegistration::class); }
    public function marks() { return $this->hasMany(ExamMark::class); }
}
