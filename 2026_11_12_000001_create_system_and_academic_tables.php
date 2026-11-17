<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('city');
            $table->string('state');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('university_affiliation')->nullable();
            $table->string('aishe_code', 30)->nullable();
            $table->string('naac_grade', 10)->nullable();
            $table->string('principal_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name');
            $table->string('module', 60);
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->after('role_id')->constrained()->nullOnDelete();
            $table->foreignId('college_id')->nullable()->after('campus_id')->constrained()->nullOnDelete();
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('status', 20)->default('Active')->after('phone');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 50)->default('general');
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);
            $table->string('module', 60);
            $table->string('record_type')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('hod_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('level', ['Undergraduate', 'Postgraduate', 'Diploma', 'Certificate'])->default('Undergraduate');
            $table->integer('duration_years')->default(3);
            $table->integer('total_semesters')->default(6);
            $table->integer('total_credits')->default(120);
            $table->integer('intake_capacity')->default(60);
            $table->decimal('cutoff_percentage', 5, 2)->default(50.00);
            $table->boolean('nep_2020_enabled')->default(true);
            $table->boolean('cbcs_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->enum('term', ['Odd', 'Even', 'Annual'])->default('Odd');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('code', 25)->unique();
            $table->string('name');
            $table->integer('semester')->default(1);
            $table->decimal('credits', 4, 1)->default(4.0);
            $table->enum('subject_type', ['Core', 'Elective', 'Practical', 'AECC', 'SEC', 'GE', 'Project'])->default('Core');
            $table->integer('max_internal_marks')->default(30);
            $table->integer('max_external_marks')->default(70);
            $table->text('syllabus_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('event_type', ['Academic', 'Holiday', 'Examination', 'Cultural', 'Workshop'])->default('Academic');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('academic_sessions');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('campus_id');
            $table->dropConstrainedForeignId('college_id');
            $table->dropColumn(['phone', 'status']);
        });
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('financial_years');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('campuses');
    }
};

