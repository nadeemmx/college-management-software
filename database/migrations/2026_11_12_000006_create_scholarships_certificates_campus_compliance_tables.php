<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('scheme_type', ['Government', 'Institutional', 'Merit', 'Need-Based', 'SC/ST/OBC/EWS'])->default('Government');
            $table->decimal('amount', 12, 2);
            $table->string('eligibility_criteria')->nullable();
            $table->foreignId('academic_session_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['Applied', 'Verified', 'Approved', 'Disbursed', 'Rejected'])->default('Applied');
            $table->decimal('disbursed_amount', 12, 2)->default(0);
            $table->date('disbursed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_no', 40)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('certificate_type', ['Bonafide', 'Transfer', 'Migration', 'Character', 'NOC', 'Course Completion', 'Provisional', 'Degree', 'Duplicate'])->default('Bonafide');
            $table->date('issue_date')->nullable();
            $table->enum('status', ['Requested', 'Approved', 'Issued', 'Rejected'])->default('Requested');
            $table->string('verification_token', 64)->unique();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 30)->unique();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('category', ['Academic', 'Anti-Ragging', 'Harassment', 'Hostel', 'Fee', 'General'])->default('General');
            $table->boolean('is_confidential')->default(false);
            $table->string('subject');
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Submitted', 'Under Review', 'Resolved', 'Closed'])->default('Submitted');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('incident_date');
            $table->text('description');
            $table->string('action_taken');
            $table->text('counselling_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('campus_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['Cultural', 'Sports', 'Seminar', 'Workshop', 'Club', 'Annual Fest'])->default('Seminar');
            $table->date('event_date');
            $table->string('venue');
            $table->decimal('budget', 12, 2)->default(0);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->enum('status', ['Planned', 'Ongoing', 'Completed', 'Cancelled'])->default('Planned');
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->boolean('certificate_issued')->default(false);
            $table->timestamps();
        });

        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->integer('graduation_year');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('current_company')->nullable();
            $table->string('designation')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_mentor')->default(false);
            $table->decimal('donation_total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('accreditation_records', function (Blueprint $table) {
            $table->id();
            $table->enum('framework', ['NAAC', 'NIRF', 'AISHE', 'UGC', 'AICTE', 'NBA', 'IQAC'])->default('NAAC');
            $table->string('cycle_year', 20);
            $table->string('criterion', 80);
            $table->string('metric_code', 30)->nullable();
            $table->string('title');
            $table->string('score_or_value', 60)->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('evidence_url')->nullable();
            $table->enum('status', ['Draft', 'Submitted', 'Verified', 'Approved'])->default('Verified');
            $table->timestamps();
        });

        Schema::create('research_records', function (Blueprint $table) {
            $table->id();
            $table->enum('record_type', ['Faculty Publication', 'Student Project', 'Research Grant', 'Patent', 'Conference', 'Incubation'])->default('Faculty Publication');
            $table->string('title');
            $table->string('lead_name');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('funding_agency')->nullable();
            $table->decimal('grant_amount', 12, 2)->default(0);
            $table->string('publication_venue')->nullable();
            $table->string('patent_no', 50)->nullable();
            $table->integer('year')->default(2026);
            $table->enum('status', ['Ongoing', 'Published', 'Granted', 'Completed'])->default('Published');
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('target_audience', ['All', 'Students', 'Faculty', 'Parents', 'Department'])->default('All');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['Portal', 'Email', 'SMS', 'WhatsApp', 'Push'])->default('Portal');
            $table->boolean('is_emergency')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_name');
            $table->string('recipient_contact');
            $table->enum('channel', ['Email', 'SMS', 'WhatsApp', 'Push'])->default('Email');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['Queued', 'Sent', 'Failed'])->default('Sent');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('research_records');
        Schema::dropIfExists('accreditation_records');
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('campus_events');
        Schema::dropIfExists('disciplinary_records');
        Schema::dropIfExists('grievances');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('scholarship_applications');
        Schema::dropIfExists('scholarships');
    }
};

