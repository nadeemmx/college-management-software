<?php

namespace App\Models;

class PlacementDrive extends BaseModel
{
    public function company() { return $this->belongsTo(PlacementCompany::class, "placement_company_id"); }
    public function applications() { return $this->hasMany(PlacementApplication::class); }
}
