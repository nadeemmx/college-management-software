<?php

namespace App\Models;

class Student extends BaseModel
{
    public function user() { return $this->belongsTo(User::class); }
    public function college() { return $this->belongsTo(College::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function classSection() { return $this->belongsTo(ClassSection::class); }
    public function guardians() { return $this->hasMany(StudentGuardian::class); }
    public function documents() { return $this->hasMany(StudentDocument::class); }
    public function attendances() { return $this->hasMany(StudentAttendance::class); }
    public function examMarks() { return $this->hasMany(ExamMark::class); }
    public function feeAccounts() { return $this->hasMany(StudentFeeAccount::class); }
    public function feePayments() { return $this->hasMany(FeePayment::class); }
    public function certificates() { return $this->hasMany(Certificate::class); }
}
