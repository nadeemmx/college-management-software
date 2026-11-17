<?php

use App\Http\Controllers\AcademicController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusCommunityController;
use App\Http\Controllers\CareerComplianceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\FacilityLmsController;
use App\Http\Controllers\FeeFinanceController;
use App\Http\Controllers\HrPayrollController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Guest-only Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Everything below requires authentication — no one can use the system without logging in
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role Portals
    Route::get('/portal/student', [PortalController::class, 'studentPortal'])
        ->middleware('role:super_admin,student')
        ->name('portal.student');
    Route::post('/portal/student/submit-assignment', [PortalController::class, 'submitAssignment'])
        ->middleware('role:super_admin,student')
        ->name('portal.student.submit');

    Route::get('/portal/faculty', [PortalController::class, 'facultyPortal'])
        ->middleware('role:super_admin,faculty')
        ->name('portal.faculty');
    Route::post('/portal/faculty/apply-leave', [PortalController::class, 'applyLeave'])
        ->middleware('role:super_admin,faculty')
        ->name('portal.faculty.leave');

    Route::get('/portal/parent', [PortalController::class, 'parentPortal'])
        ->middleware('role:super_admin,parent')
        ->name('portal.parent');

    // Printable student/certificate/receipt views accessible to authenticated users
    Route::get('/students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id_card');
    Route::get('/students/{student}/marksheet', [ExaminationController::class, 'marksheet'])->name('exams.marksheet');
    Route::get('/exams/{exam}/admit-card/{student}', [ExaminationController::class, 'admitCard'])->name('exams.admit_card');
    Route::get('/fees/payments/{payment}/receipt', [FeeFinanceController::class, 'feeReceipt'])->name('fees.receipt');
    Route::get('/certificates/{certificate}/print', [CareerComplianceController::class, 'printCertificate'])->name('certificates.print');
    Route::get('/verify-certificate/{token}', [CareerComplianceController::class, 'verifyCertificate'])->name('certificates.verify');

    // Faculty & Academic Staff accessible modules
    Route::middleware('role:super_admin,faculty')->group(function () {
        Route::get('/timetable', [AcademicController::class, 'timetable'])->name('timetable.index');
        Route::post('/timetable/sections', [AcademicController::class, 'storeSection'])->name('timetable.sections.store');
        Route::post('/timetable/slots', [AcademicController::class, 'storeTimetableSlot'])->name('timetable.slots.store');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/students', [AttendanceController::class, 'markStudentAttendance'])->name('attendance.students.mark');
        Route::post('/attendance/employees', [AttendanceController::class, 'markEmployeeAttendance'])->name('attendance.employees.mark');
        Route::post('/attendance/shortage-alerts', [AttendanceController::class, 'sendShortageAlerts'])->name('attendance.shortage_alerts');
        Route::post('/attendance/corrections/{correction}', [AttendanceController::class, 'updateCorrection'])->name('attendance.corrections.update');

        Route::get('/exams', [ExaminationController::class, 'index'])->name('exams.index');
        Route::post('/exams', [ExaminationController::class, 'storeExam'])->name('exams.store');
        Route::post('/exams/schedules', [ExaminationController::class, 'storeSchedule'])->name('exams.schedules.store');
        Route::post('/exams/marks', [ExaminationController::class, 'storeMark'])->name('exams.marks.store');
        Route::post('/exams/{exam}/publish', [ExaminationController::class, 'togglePublish'])->name('exams.publish');

        Route::get('/lms', [FacilityLmsController::class, 'lms'])->name('lms.index');
        Route::post('/lms/materials', [FacilityLmsController::class, 'storeMaterial'])->name('lms.materials.store');
        Route::post('/lms/assignments', [FacilityLmsController::class, 'storeAssignment'])->name('lms.assignments.store');
        Route::post('/lms/discussions', [FacilityLmsController::class, 'storeDiscussion'])->name('lms.discussions.store');
        Route::post('/lms/submissions/{submission}/grade', [FacilityLmsController::class, 'gradeSubmission'])->name('lms.submissions.grade');
    });

    // Library module (Admin, Librarian, Faculty)
    Route::middleware('role:super_admin,librarian,faculty')->group(function () {
        Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
        Route::post('/library/books', [LibraryController::class, 'storeBook'])->name('library.books.store');
        Route::post('/library/issue', [LibraryController::class, 'issueBook'])->name('library.issue');
        Route::post('/library/issues/{issue}/return', [LibraryController::class, 'returnBook'])->name('library.return');
        Route::post('/library/issues/{issue}/renew', [LibraryController::class, 'renewBook'])->name('library.renew');
    });

    // Finance & Fee Accountant modules (Admin, Accountant)
    Route::middleware('role:super_admin,accountant')->group(function () {
        Route::get('/fees', [FeeFinanceController::class, 'fees'])->name('fees.index');
        Route::post('/fees/structures', [FeeFinanceController::class, 'storeFeeStructure'])->name('fees.structures.store');
        Route::post('/fees/accounts/{account}/collect', [FeeFinanceController::class, 'collectFee'])->name('fees.collect');

        Route::get('/finance', [FeeFinanceController::class, 'finance'])->name('finance.index');
        Route::post('/finance/vouchers', [FeeFinanceController::class, 'storeVoucher'])->name('finance.vouchers.store');

        Route::get('/procurement', [FeeFinanceController::class, 'procurement'])->name('procurement.index');
        Route::post('/procurement/requisitions', [FeeFinanceController::class, 'storeRequisition'])->name('procurement.requisitions.store');
        Route::post('/procurement/orders', [FeeFinanceController::class, 'storePurchaseOrder'])->name('procurement.orders.store');

        Route::get('/payroll', [HrPayrollController::class, 'payroll'])->name('payroll.index');
        Route::post('/payroll/process', [HrPayrollController::class, 'processPayroll'])->name('payroll.process');
        Route::get('/payroll/{payroll}/payslip', [HrPayrollController::class, 'payslip'])->name('payroll.payslip');
    });

    // Admin ERP Modules (Super Admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/apply', [AdmissionController::class, 'publicApply'])->name('admission.public');
        Route::post('/apply', [AdmissionController::class, 'submitPublicApplication'])->name('admission.public.store');
        Route::get('/track-application', [AdmissionController::class, 'trackApplication'])->name('admission.track');

        Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
        Route::post('/admissions/merit-list', [AdmissionController::class, 'generateMeritList'])->name('admissions.merit');
        Route::post('/admissions/{application}/status', [AdmissionController::class, 'updateStatus'])->name('admissions.status');
        Route::get('/admissions/{application}/receipt', [AdmissionController::class, 'receipt'])->name('admissions.receipt');

        Route::get('/enquiries', [AdmissionController::class, 'enquiries'])->name('enquiries.index');
        Route::post('/enquiries', [AdmissionController::class, 'storeEnquiry'])->name('enquiries.store');
        Route::post('/enquiries/{enquiry}/followup', [AdmissionController::class, 'addFollowup'])->name('enquiries.followup');
        Route::post('/enquiries/bulk-notify', [AdmissionController::class, 'bulkNotifyEnquiries'])->name('enquiries.bulk_notify');

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::post('/students/{student}/status', [StudentController::class, 'updateStatus'])->name('students.status');
        Route::post('/students/{student}/documents', [StudentController::class, 'uploadDocument'])->name('students.documents');

        Route::get('/academics', [AcademicController::class, 'index'])->name('academics.index');
        Route::post('/academics/departments', [AcademicController::class, 'storeDepartment'])->name('academics.departments.store');
        Route::post('/academics/courses', [AcademicController::class, 'storeCourse'])->name('academics.courses.store');
        Route::post('/academics/subjects', [AcademicController::class, 'storeSubject'])->name('academics.subjects.store');
        Route::post('/academics/calendar', [AcademicController::class, 'storeCalendar'])->name('academics.calendar.store');

        Route::get('/hr', [HrPayrollController::class, 'index'])->name('hr.index');
        Route::post('/hr/employees', [HrPayrollController::class, 'storeEmployee'])->name('hr.employees.store');
        Route::post('/hr/leaves/{leave}', [HrPayrollController::class, 'updateLeave'])->name('hr.leaves.update');

        Route::get('/hostel', [FacilityLmsController::class, 'hostel'])->name('hostel.index');
        Route::post('/hostel/allocate', [FacilityLmsController::class, 'allocateHostel'])->name('hostel.allocate');
        Route::post('/hostel/activities', [FacilityLmsController::class, 'storeHostelActivity'])->name('hostel.activities.store');

        Route::get('/transport', [FacilityLmsController::class, 'transport'])->name('transport.index');
        Route::post('/transport/allocate', [FacilityLmsController::class, 'allocateTransport'])->name('transport.allocate');

        Route::get('/placements', [CareerComplianceController::class, 'placements'])->name('placements.index');
        Route::post('/placements/drives', [CareerComplianceController::class, 'storeDrive'])->name('placements.drives.store');
        Route::post('/placements/apply', [CareerComplianceController::class, 'applyDrive'])->name('placements.apply');
        Route::post('/placements/applications/{application}/status', [CareerComplianceController::class, 'updatePlacementStatus'])->name('placements.status');

        Route::get('/scholarships', [CareerComplianceController::class, 'scholarships'])->name('scholarships.index');
        Route::post('/scholarships/apply', [CareerComplianceController::class, 'applyScholarship'])->name('scholarships.apply');
        Route::post('/scholarships/applications/{application}/status', [CareerComplianceController::class, 'updateScholarshipStatus'])->name('scholarships.status');

        Route::get('/certificates', [CareerComplianceController::class, 'certificates'])->name('certificates.index');
        Route::post('/certificates', [CareerComplianceController::class, 'issueCertificate'])->name('certificates.store');

        Route::get('/grievances', [CareerComplianceController::class, 'grievances'])->name('grievances.index');
        Route::post('/grievances', [CareerComplianceController::class, 'storeGrievance'])->name('grievances.store');
        Route::post('/grievances/{grievance}/resolve', [CareerComplianceController::class, 'resolveGrievance'])->name('grievances.resolve');
        Route::post('/disciplinary', [CareerComplianceController::class, 'storeDisciplinary'])->name('disciplinary.store');

        Route::get('/events-alumni', [CampusCommunityController::class, 'eventsAlumni'])->name('community.events_alumni');
        Route::post('/events', [CampusCommunityController::class, 'storeEvent'])->name('events.store');
        Route::post('/alumni', [CampusCommunityController::class, 'storeAlumnus'])->name('alumni.store');

        Route::get('/accreditation-research', [CampusCommunityController::class, 'accreditationResearch'])->name('community.accreditation_research');
        Route::post('/accreditation', [CampusCommunityController::class, 'storeAccreditation'])->name('accreditation.store');
        Route::post('/research', [CampusCommunityController::class, 'storeResearch'])->name('research.store');

        Route::get('/communication', [CampusCommunityController::class, 'communication'])->name('community.communication');
        Route::post('/announcements', [CampusCommunityController::class, 'storeAnnouncement'])->name('announcements.store');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/campuses', [SettingsController::class, 'storeCampus'])->name('settings.campuses.store');
        Route::post('/settings/campuses/{campus}', [SettingsController::class, 'updateCampus'])->name('settings.campuses.update');
        Route::post('/settings/colleges', [SettingsController::class, 'storeCollege'])->name('settings.colleges.store');
        Route::post('/settings/colleges/{college}', [SettingsController::class, 'updateCollege'])->name('settings.colleges.update');
        Route::post('/settings/update', [SettingsController::class, 'updateSetting'])->name('settings.update');
        Route::get('/settings/export-students', [SettingsController::class, 'exportStudentsCsv'])->name('settings.export_students');
        Route::get('/settings/backup', [SettingsController::class, 'backupDatabase'])->name('settings.backup');
    });
});
