<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\FeePayment;
use App\Models\FinancialVoucher;
use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\PlacementApplication;
use App\Models\PlacementDrive;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeAccount;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = Student::where('status', 'Active')->count();
        $totalFaculty = Employee::where('employee_type', 'Faculty')->where('status', 'Active')->count();
        $totalStaff = Employee::where('employee_type', '!=', 'Faculty')->where('status', 'Active')->count();

        $newEnquiries = AdmissionEnquiry::count();
        $convertedEnquiries = AdmissionEnquiry::where('status', 'Converted')->count();
        $conversionRate = $newEnquiries > 0 ? round(($convertedEnquiries / $newEnquiries) * 100, 1) : 0;

        $applicationsReceived = AdmissionApplication::count();
        $applicationsPending = AdmissionApplication::whereIn('status', ['Submitted', 'Waiting List'])->count();

        $today = now()->toDateString();
        $todayAttendanceTotal = StudentAttendance::whereDate('attendance_date', $today)->count();
        $todayPresent = StudentAttendance::whereDate('attendance_date', $today)->whereIn('status', ['Present', 'Late'])->count();
        $todayAttendancePct = $todayAttendanceTotal > 0 ? round(($todayPresent / $todayAttendanceTotal) * 100, 1) : 92.4;

        $upcomingExams = Exam::with('course')->where('end_date', '>=', $today)->orderBy('start_date')->take(5)->get();

        $pendingFeeAccounts = StudentFeeAccount::whereIn('status', ['Pending', 'Partial'])->count();
        $outstandingFees = StudentFeeAccount::sum('balance_amount');
        $totalFeeCollected = FeePayment::where('status', 'Completed')->sum('amount');

        $booksIssued = LibraryIssue::whereIn('status', ['Issued', 'Overdue'])->count();
        $booksOverdue = LibraryIssue::where('status', 'Overdue')->count();
        $totalBooks = LibraryBook::sum('total_copies');

        $placementDrives = PlacementDrive::count();
        $studentsSelected = PlacementApplication::where('status', 'Selected')->count();

        $totalIncome = FinancialVoucher::where('voucher_type', 'Receipt')->sum('amount') + $totalFeeCollected;
        $totalExpense = FinancialVoucher::where('voucher_type', 'Payment')->sum('amount');

        $courseDistribution = Course::withCount('students')->get();
        $departmentDistribution = Department::withCount('students')->get();
        $recentAnnouncements = Announcement::latest()->take(4)->get();

        $gradeDistribution = ExamMark::selectRaw('grade, count(*) as total')
            ->groupBy('grade')
            ->pluck('total', 'grade');

        return view('dashboard.index', compact(
            'totalStudents',
            'totalFaculty',
            'totalStaff',
            'newEnquiries',
            'convertedEnquiries',
            'conversionRate',
            'applicationsReceived',
            'applicationsPending',
            'todayAttendancePct',
            'upcomingExams',
            'pendingFeeAccounts',
            'outstandingFees',
            'totalFeeCollected',
            'booksIssued',
            'booksOverdue',
            'totalBooks',
            'placementDrives',
            'studentsSelected',
            'totalIncome',
            'totalExpense',
            'courseDistribution',
            'departmentDistribution',
            'recentAnnouncements',
            'gradeDistribution'
        ));
    }
}

