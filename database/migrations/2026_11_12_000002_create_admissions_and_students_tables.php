<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_no', 30)->unique();
            $table->string('student_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->enum('source', ['Walk-in', 'Telephone', 'Online', 'Campaign', 'Referral'])->default('Walk-in');
            $table->string('campaign_name')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('counsellor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['New', 'Follow-up', 'Converted', 'Lost'])->default('New');
            $table->string('lost_reason')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('enquiry_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_enquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('followup_at');
            $table->text('notes');
            $table->string('outcome', 60)->nullable();
            $table->timestamps();
        });

        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 30)->unique();
            $table->foreignId('academic_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->enum('category', ['General', 'OBC', 'SC', 'ST', 'EWS'])->default('General');
            $table->string('aadhaar_number', 12)->nullable();
            $table->boolean('aadhaar_consent')->default(false);
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->text('address')->nullable();
            $table->string('class_10_board', 80)->nullable();
            $table->decimal('class_10_percentage', 5, 2)->nullable();
            $table->string('class_12_board', 80)->nullable();
            $table->decimal('class_12_percentage', 5, 2)->nullable();
            $table->string('previous_qualification')->nullable();
            $table->string('entrance_exam_name', 80)->nullable();
            $table->decimal('entrance_score', 6, 2)->nullable();
            $table->decimal('merit_score', 6, 2)->nullable();
            $table->integer('merit_rank')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->json('documents_json')->nullable();
            $table->decimal('application_fee_amount', 10, 2)->default(500.00);
            $table->enum('application_fee_status', ['Pending', 'Paid', 'Refunded'])->default('Pending');
            $table->string('payment_txn_id', 80)->nullable();
            $table->enum('status', ['Submitted', 'Verified', 'Merit Listed', 'Waiting List', 'Approved', 'Rejected', 'Confirmed', 'Cancelled'])->default('Submitted');
            $table->string('registration_number', 40)->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('college_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('current_semester')->default(1);
            $table->unsignedBigInteger('class_section_id')->nullable();
            $table->string('unique_student_id', 30)->unique();
            $table->string('admission_number', 30)->unique();
            $table->string('registration_number', 40)->nullable();
            $table->string('roll_number', 30)->nullable();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('blood_group', 10)->nullable();
            $table->enum('category', ['General', 'OBC', 'SC', 'ST', 'EWS'])->default('General');
            $table->string('religion', 40)->nullable();
            $table->string('aadhaar_last4', 4)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('correspondence_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->text('previous_academic_history')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['Active', 'Passed Out', 'Suspended', 'Withdrawn'])->default('Active');
            $table->text('transfer_migration_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('relation', ['Father', 'Mother', 'Guardian'])->default('Father');
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('annual_income', 12, 2)->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 80);
            $table->string('file_path');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_guardians');
        Schema::dropIfExists('students');
        Schema::dropIfExists('admission_applications');
        Schema::dropIfExists('enquiry_followups');
        Schema::dropIfExists('admission_enquiries');
    }
};

