<?php

namespace App\Models;

class Course extends BaseModel
{
    public function department() { return $this->belongsTo(Department::class); }
    public function subjects() { return $this->hasMany(Subject::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function feeStructures() { return $this->hasMany(FeeStructure::class); }
    public function classSections() { return $this->hasMany(ClassSection::class); }
}
