<?php

namespace App\Models;

class FeePayment extends BaseModel
{
    public function feeAccount() { return $this->belongsTo(StudentFeeAccount::class, "student_fee_account_id"); }
    public function student() { return $this->belongsTo(Student::class); }
}
