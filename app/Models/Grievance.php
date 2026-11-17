<?php

namespace App\Models;

class Grievance extends BaseModel
{
    public function student() { return $this->belongsTo(Student::class); }
    public function assignee() { return $this->belongsTo(User::class, "assigned_to"); }
}
