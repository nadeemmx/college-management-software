<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Boys', 'Girls', 'Co-Ed'])->default('Boys');
            $table->foreignId('warden_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->integer('total_rooms')->default(50);
            $table->integer('capacity')->default(150);
            $table->decimal('monthly_fee', 10, 2)->default(4500.00);
            $table->decimal('mess_fee', 10, 2)->default(3500.00);
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->string('floor', 20)->default('Ground');
            $table->string('room_no', 20);
            $table->integer('capacity')->default(3);
            $table->integer('occupied_beds')->default(0);
            $table->enum('room_type', ['Single', 'Double', 'Triple', 'Dormitory'])->default('Triple');
            $table->timestamps();
        });

        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hostel_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('bed_no', 10);
            $table->date('check_in_date');
            $table->enum('status', ['Active', 'Vacated'])->default('Active');
            $table->timestamps();
        });

        Schema::create('hostel_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('activity_type', ['Visitor', 'Complaint', 'Maintenance', 'Outing Request', 'Attendance'])->default('Complaint');
            $table->string('title');
            $table->text('details')->nullable();
            $table->enum('status', ['Open', 'Approved', 'Resolved', 'Closed'])->default('Open');
            $table->timestamps();
        });

        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no', 30)->unique();
            $table->enum('vehicle_type', ['Bus', 'Mini Bus', 'Van'])->default('Bus');
            $table->integer('capacity')->default(45);
            $table->string('driver_name');
            $table->string('driver_phone', 20);
            $table->string('driver_license', 40)->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->string('gps_device_id', 50)->nullable();
            $table->decimal('last_fuel_liters', 8, 2)->default(0);
            $table->text('maintenance_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->foreignId('transport_vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->json('stops_json')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(1800.00);
            $table->timestamps();
        });

        Schema::create('transport_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('pickup_stop');
            $table->date('start_date');
            $table->enum('status', ['Active', 'Cancelled'])->default('Active');
            $table->timestamps();
        });

        Schema::create('lms_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('title');
            $table->enum('material_type', ['PDF', 'Document', 'Video Lecture', 'Recorded Class', 'Quiz'])->default('PDF');
            $table->string('file_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->integer('max_marks')->default(20);
            $table->timestamps();
        });

        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('submission_url')->nullable();
            $table->text('submission_notes')->nullable();
            $table->dateTime('submitted_at');
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('lms_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('topic');
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('placement_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry', 80);
            $table->string('hr_name')->nullable();
            $table->string('hr_email')->nullable();
            $table->string('hr_phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        Schema::create('placement_drives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_company_id')->constrained()->cascadeOnDelete();
            $table->string('job_title');
            $table->decimal('package_lpa', 6, 2);
            $table->decimal('eligibility_cgpa', 4, 2)->default(6.00);
            $table->date('drive_date');
            $table->string('location')->default('Campus');
            $table->enum('drive_type', ['Full-Time', 'Internship'])->default('Full-Time');
            $table->enum('status', ['Upcoming', 'Ongoing', 'Completed'])->default('Upcoming');
            $table->timestamps();
        });

        Schema::create('placement_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_drive_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('resume_url')->nullable();
            $table->integer('aptitude_score')->nullable();
            $table->dateTime('interview_date')->nullable();
            $table->enum('status', ['Applied', 'Shortlisted', 'Interviewed', 'Selected', 'Rejected'])->default('Applied');
            $table->string('offer_letter_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_applications');
        Schema::dropIfExists('placement_drives');
        Schema::dropIfExists('placement_companies');
        Schema::dropIfExists('lms_discussions');
        Schema::dropIfExists('lms_submissions');
        Schema::dropIfExists('lms_assignments');
        Schema::dropIfExists('lms_materials');
        Schema::dropIfExists('transport_allocations');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('transport_vehicles');
        Schema::dropIfExists('hostel_activities');
        Schema::dropIfExists('hostel_allocations');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
    }
};

