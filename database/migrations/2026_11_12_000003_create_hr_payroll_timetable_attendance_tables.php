<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('college_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_id', 30)->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->enum('employee_type', ['Faculty', 'Non-Teaching', 'Admin'])->default('Faculty');
            $table->string('designation');
            $table->string('qualification')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('specialization')->nullable();
            $table->date('joining_date');
            $table->date('resignation_date')->nullable();
            $table->enum('status', ['Active', 'On Leave', 'Resigned'])->default('Active');
            $table->decimal('basic_salary', 12, 2)->default(35000.00);
            $table->string('bank_account_no', 40)->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('pan_number', 15)->nullable();
            $table->string('uan_number', 20)->nullable();
            $table->string('esi_number', 20)->nullable();
            $table->integer('weekly_workload_hours')->default(18);
            $table->timestamps();
        });

        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('leave_type', ['Casual', 'Medical', 'Earned', 'Maternity', 'Duty', 'LWP'])->default('Casual');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('days')->default(1);
            $table->text('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('period', 40);
            $table->decimal('rating', 3, 1)->default(4.0);
            $table->string('promoted_to')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('da', 12, 2)->default(0);
            $table->decimal('ta', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->boolean('pf_enabled')->default(true);
            $table->boolean('esi_enabled')->default(false);
            $table->boolean('pt_enabled')->default(true);
            $table->decimal('tds_monthly', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->integer('working_days')->default(26);
            $table->integer('leave_deduction_days')->default(0);
            $table->decimal('gross_salary', 12, 2);
            $table->decimal('pf_amount', 10, 2)->default(0);
            $table->decimal('esi_amount', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('tds_amount', 10, 2)->default(0);
            $table->decimal('loan_deduction', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('arrears', 10, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->enum('status', ['Draft', 'Processed', 'Paid'])->default('Processed');
            $table->date('paid_at')->nullable();
            $table->string('bank_ref', 60)->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'month', 'year']);
        });

        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('semester')->default(1);
            $table->string('section_name', 20)->default('A');
            $table->string('room_no', 30)->nullable();
            $table->foreignId('class_teacher_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('subject_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('substitute_faculty_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_no', 30);
            $table->enum('slot_type', ['Lecture', 'Practical', 'Lab', 'Special'])->default('Lecture');
            $table->timestamps();
        });

        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Excused'])->default('Present');
            $table->enum('method', ['Manual', 'QR', 'Biometric', 'RFID'])->default('Manual');
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day', 'On Leave'])->default('Present');
            $table->enum('method', ['Manual', 'Biometric', 'RFID'])->default('Biometric');
            $table->timestamps();
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->text('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('employee_attendances');
        Schema::dropIfExists('student_attendances');
        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('subject_allocations');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('salary_structures');
        Schema::dropIfExists('employee_evaluations');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employees');
    }
};

