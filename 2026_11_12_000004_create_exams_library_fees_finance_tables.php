<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('exam_type', ['Internal', 'Mid-Semester', 'End-Semester', 'Practical', 'Supplementary', 'Backlog'])->default('End-Semester');
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('semester')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_no', 30);
            $table->foreignId('invigilator_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->integer('max_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->string('question_paper_code', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('exam_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('seat_no', 30)->nullable();
            $table->string('admit_card_number', 40)->nullable();
            $table->boolean('is_eligible')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('internal_marks', 5, 2)->default(0);
            $table->decimal('external_marks', 5, 2)->default(0);
            $table->decimal('total_marks', 5, 2)->default(0);
            $table->string('grade', 5)->nullable();
            $table->decimal('grade_point', 4, 2)->default(0);
            $table->decimal('credits', 4, 1)->default(4.0);
            $table->enum('result_status', ['Pass', 'Fail', 'Backlog', 'Absent'])->default('Pass');
            $table->boolean('is_verified')->default(false);
            $table->enum('reevaluation_status', ['None', 'Applied', 'Completed'])->default('None');
            $table->timestamps();
            $table->unique(['exam_id', 'subject_id', 'student_id']);
        });

        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 30)->nullable();
            $table->string('accession_no', 30)->unique();
            $table->string('barcode', 40)->nullable();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('subject_category', 80);
            $table->string('rack_location', 40)->nullable();
            $table->enum('book_type', ['Regular', 'Reference', 'Journal', 'Magazine', 'E-Book'])->default('Regular');
            $table->string('ebook_url')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->enum('status', ['Available', 'Out of Stock', 'Damaged', 'Lost'])->default('Available');
            $table->timestamps();
        });

        Schema::create('library_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('member_type', ['Student', 'Faculty'])->default('Student');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['Issued', 'Returned', 'Reserved', 'Overdue', 'Lost'])->default('Issued');
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->timestamps();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('semester')->default(1);
            $table->enum('category', ['All', 'General', 'OBC', 'SC', 'ST', 'EWS'])->default('All');
            $table->decimal('admission_fee', 10, 2)->default(0);
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->decimal('exam_fee', 10, 2)->default(0);
            $table->decimal('library_fee', 10, 2)->default(0);
            $table->decimal('lab_fee', 10, 2)->default(0);
            $table->decimal('hostel_fee', 10, 2)->default(0);
            $table->decimal('transport_fee', 10, 2)->default(0);
            $table->decimal('misc_fee', 10, 2)->default(0);
            $table->decimal('total_fee', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('student_fee_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_session_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('concession_amount', 10, 2)->default(0);
            $table->decimal('scholarship_amount', 10, 2)->default(0);
            $table->decimal('late_penalty', 10, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['Pending', 'Partial', 'Paid'])->default('Pending');
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_no', 40)->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['UPI', 'Card', 'NetBanking', 'Cash', 'Cheque', 'DD'])->default('UPI');
            $table->string('transaction_ref', 80)->nullable();
            $table->date('payment_date');
            $table->enum('status', ['Completed', 'Refunded'])->default('Completed');
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('account_type', ['Asset', 'Liability', 'Equity', 'Income', 'Expense']);
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('financial_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 30)->unique();
            $table->enum('voucher_type', ['Payment', 'Receipt', 'Contra', 'Journal']);
            $table->date('voucher_date');
            $table->foreignId('debit_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('credit_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('tds_amount', 10, 2)->default(0);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('narration');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('department_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('financial_year', 20);
            $table->decimal('allocated_amount', 14, 2);
            $table->decimal('utilized_amount', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 30)->unique();
            $table->string('name');
            $table->string('category', 60);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 14, 2);
            $table->decimal('depreciation_rate', 5, 2)->default(10.00);
            $table->decimal('current_value', 14, 2);
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gstin', 20)->nullable();
            $table->string('pan', 15)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('category', 60)->default('General');
            $table->timestamps();
        });

        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('req_no', 30)->unique();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('item_description');
            $table->integer('quantity')->default(1);
            $table->decimal('estimated_cost', 12, 2);
            $table->enum('status', ['Pending', 'Approved', 'PO Created', 'Rejected'])->default('Pending');
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no', 30)->unique();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_requisition_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date');
            $table->decimal('total_amount', 14, 2);
            $table->enum('status', ['Ordered', 'Goods Received', 'Invoiced', 'Paid'])->default('Ordered');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 30)->unique();
            $table->string('name');
            $table->enum('category', ['Lab Equipment', 'Furniture', 'Stationery', 'IT Hardware', 'Sports'])->default('Lab Equipment');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(0);
            $table->string('unit', 20)->default('Pcs');
            $table->integer('reorder_level')->default(5);
            $table->enum('condition_status', ['Good', 'Needs Maintenance', 'Damaged'])->default('Good');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requisitions');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('department_budgets');
        Schema::dropIfExists('financial_vouchers');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fee_accounts');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('library_issues');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_registrations');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
    }
};

