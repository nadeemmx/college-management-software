<?php

namespace App\Models;

class Department extends BaseModel
{
    public function college() { return $this->belongsTo(College::class); }
    public function courses() { return $this->hasMany(Course::class); }
    public function employees() { return $this->hasMany(Employee::class); }
    public function students() { return $this->hasMany(Student::class); }
}
