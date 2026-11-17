<?php

namespace App\Models;

class PlacementApplication extends BaseModel
{
    public function drive() { return $this->belongsTo(PlacementDrive::class, "placement_drive_id"); }
    public function student() { return $this->belongsTo(Student::class); }
}
