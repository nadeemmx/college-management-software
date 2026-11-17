<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Employee;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamRegistration;
use App\Models\ExamSchedule;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExaminationController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with(['course', 'academicSession', 'schedules.subject', 'schedules.invigilator'])->latest()->get();
        $courses = Course::all();
        $sessions = AcademicSession::all();
        $subjects = Subject::all();
        $faculty = Employee::where('employee_type', 'Faculty')->get();
        $students = Student::with('course')->where('status', 'Active')->get();
        $marks = ExamMark::with(['exam', 'subject', 'student'])->latest()->take(50)->get();

        return view('exams.index', compact('exams', 'courses', 'sessions', 'subjects', 'faculty', 'students', 'marks'));
    }

    public function storeExam(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'exam_type' => 'required|in:Internal,Mid-Semester,End-Semester,Practical,Supplementary,Backlog',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        Exam::create($data);

        return back()->with('success', 'Examination created.');
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_no' => 'required|string|max:30',
            'invigilator_id' => 'nullable|exists:employees,id',
            'max_marks' => 'required|integer|min:10|max:200',
            'passing_marks' => 'required|integer|min:5|max:100',
            'question_paper_code' => 'nullable|string|max:40',
        ]);
        ExamSchedule::create($data);

        return back()->with('success', 'Exam schedule & invigilator assigned.');
    }

    public function storeMark(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'internal_marks' => 'required|numeric|min:0|max:50',
            'external_marks' => 'required|numeric|min:0|max:100',
        ]);

        $subject = Subject::findOrFail($data['subject_id']);
        $total = $data['internal_marks'] + $data['external_marks'];

        [$grade, $gp, $status] = match (true) {
            $total >= 90 => ['O', 10.0, 'Pass'],
            $total >= 80 => ['A+', 9.0, 'Pass'],
            $total >= 70 => ['A', 8.0, 'Pass'],
            $total >= 60 => ['B+', 7.0, 'Pass'],
            $total >= 50 => ['B', 6.0, 'Pass'],
            $total >= 40 => ['C', 5.0, 'Pass'],
            default => ['F', 0.0, 'Backlog'],
        };

        ExamMark::updateOrCreate(
            ['exam_id' => $data['exam_id'], 'subject_id' => $data['subject_id'], 'student_id' => $data['student_id']],
            [
                'internal_marks' => $data['internal_marks'],
                'external_marks' => $data['external_marks'],
                'total_marks' => $total,
                'grade' => $grade,
                'grade_point' => $gp,
                'credits' => $subject->credits,
                'result_status' => $status,
                'is_verified' => true,
            ]
        );

        return back()->with('success', "Marks saved! Auto-calculated Grade: {$grade} ({$gp} GP).");
    }

    public function togglePublish(Exam $exam): RedirectResponse
    {
        $exam->update(['is_published' => ! $exam->is_published]);

        return back()->with('success', 'Result publication status updated.');
    }

    public function admitCard(Exam $exam, Student $student): View
    {
        $exam->load('schedules.subject', 'course');
        $registration = ExamRegistration::firstOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            ['seat_no' => 'SEAT-' . rand(100, 999), 'admit_card_number' => 'ADM-' . $exam->id . '-' . $student->id]
        );

        return view('exams.admit_card', compact('exam', 'student', 'registration'));
    }

    public function marksheet(Student $student): View
    {
        $student->load('course.department.college', 'examMarks.subject', 'examMarks.exam');
        $marks = $student->examMarks;
        $totalCredits = $marks->sum('credits');
        $weightedGp = $marks->sum(fn ($m) => $m->credits * $m->grade_point);
        $sgpa = $totalCredits > 0 ? round($weightedGp / $totalCredits, 2) : 0;

        return view('exams.marksheet', compact('student', 'marks', 'sgpa', 'totalCredits'));
    }
}

