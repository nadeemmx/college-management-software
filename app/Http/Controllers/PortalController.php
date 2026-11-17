<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\Exam;
use App\Models\LibraryIssue;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsSubmission;
use App\Models\Student;
use App\Models\TimetableSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function studentPortal(): View
    {
        $user = Auth::user();
        $student = Student::with([
            'course.subjects',
            'department',
            'classSection',
            'attendances.subject',
            'feeAccounts.payments',
            'examMarks.subject',
            'certificates',
        ])->where('user_id', $user->id)->first() ?? Student::with([
            'course.subjects',
            'department',
            'classSection',
            'attendances.subject',
            'feeAccounts.payments',
            'examMarks.subject',
            'certificates',
        ])->first();

        $totalAtt = $student?->attendances->count() ?? 0;
        $presentAtt = $student?->attendances->whereIn('status', ['Present', 'Late'])->count() ?? 0;
        $attendancePct = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100;

        $timetable = $student && $student->class_section_id
            ? TimetableSlot::with(['subject', 'faculty'])->where('class_section_id', $student->class_section_id)->get()
            : collect();

        $exams = $student ? Exam::where('course_id', $student->course_id)->get() : collect();
        $libraryIssues = $student && $student->user_id ? LibraryIssue::with('book')->where('user_id', $student->user_id)->get() : collect();
        $materials = LmsMaterial::with('subject')->latest()->take(6)->get();
        $assignments = LmsAssignment::with('subject')->latest()->take(6)->get();
        $announcements = Announcement::whereIn('target_audience', ['All', 'Students'])->latest()->take(5)->get();

        return view('portals.student', compact(
            'student',
            'attendancePct',
            'timetable',
            'exams',
            'libraryIssues',
            'materials',
            'assignments',
            'announcements'
        ));
    }

    public function submitAssignment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lms_assignment_id' => 'required|exists:lms_assignments,id',
            'student_id' => 'required|exists:students,id',
            'submission_url' => 'required|string|max:255',
            'submission_notes' => 'nullable|string',
        ]);
        $data['submitted_at'] = now();
        LmsSubmission::create($data);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function facultyPortal(): View
    {
        $user = Auth::user();
        $faculty = Employee::with(['department', 'subjectAllocations.subject', 'subjectAllocations.classSection.course', 'leaves'])
            ->where('user_id', $user->id)->first()
            ?? Employee::with(['department', 'subjectAllocations.subject', 'subjectAllocations.classSection.course', 'leaves'])
                ->where('employee_type', 'Faculty')->first();

        $timetable = $faculty
            ? TimetableSlot::with(['classSection.course', 'subject'])->where('faculty_id', $faculty->id)->get()
            : collect();

        $students = Student::where('status', 'Active')->take(15)->get();
        $announcements = Announcement::whereIn('target_audience', ['All', 'Faculty'])->latest()->take(5)->get();

        return view('portals.faculty', compact('faculty', 'timetable', 'students', 'announcements'));
    }

    public function applyLeave(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:Casual,Medical,Earned,Maternity,Duty,LWP',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string',
        ]);
        $days = \Carbon\Carbon::parse($data['from_date'])->diffInDays(\Carbon\Carbon::parse($data['to_date'])) + 1;
        $data['days'] = $days;
        EmployeeLeave::create($data);

        return back()->with('success', "Leave application submitted for {$days} day(s).");
    }

    public function parentPortal(Request $request): View
    {
        $user = Auth::user();
        $children = Student::with([
            'course',
            'department',
            'attendances',
            'feeAccounts.payments',
            'examMarks.subject',
        ])->whereHas('guardians', fn ($q) => $q->where('parent_user_id', $user->id))->get();

        if ($children->isEmpty()) {
            $children = Student::with(['course', 'department', 'attendances', 'feeAccounts.payments', 'examMarks.subject'])->take(2)->get();
        }

        $selectedChild = $request->filled('student_id')
            ? ($children->firstWhere('id', $request->student_id) ?? $children->first())
            : $children->first();

        $totalAtt = $selectedChild?->attendances->count() ?? 0;
        $presentAtt = $selectedChild?->attendances->whereIn('status', ['Present', 'Late'])->count() ?? 0;
        $attendancePct = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100;

        $announcements = Announcement::whereIn('target_audience', ['All', 'Parents', 'Students'])->latest()->take(5)->get();

        return view('portals.parent', compact('children', 'selectedChild', 'attendancePct', 'announcements'));
    }
}

