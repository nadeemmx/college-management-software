<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['course', 'department', 'classSection', 'guardians'])->latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('full_name', 'like', "%{$s}%")
                ->orWhere('unique_student_id', 'like', "%{$s}%")
                ->orWhere('admission_number', 'like', "%{$s}%"));
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->get();
        $courses = Course::all();
        $departments = Department::all();
        $colleges = College::all();
        $sections = ClassSection::with('course')->get();

        return view('students.index', compact('students', 'courses', 'departments', 'colleges', 'sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'current_semester' => 'required|integer|min:1|max:10',
            'class_section_id' => 'nullable|exists:class_sections,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'category' => 'required|in:General,OBC,SC,ST,EWS',
            'blood_group' => 'nullable|string|max:10',
            'permanent_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
        ]);

        $studentRole = Role::where('slug', 'student')->first();
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['full_name'],
                'password' => Hash::make('password'),
                'role_id' => $studentRole?->id,
                'college_id' => $data['college_id'],
                'phone' => $data['phone'],
            ]
        );

        $seq = Student::count() + 1;
        $student = Student::create([
            'user_id' => $user->id,
            'college_id' => $data['college_id'],
            'department_id' => $data['department_id'],
            'course_id' => $data['course_id'],
            'current_semester' => $data['current_semester'],
            'class_section_id' => $data['class_section_id'] ?? null,
            'unique_student_id' => 'VV' . date('y') . 'ST' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
            'admission_number' => 'ADM-' . date('Y') . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
            'registration_number' => 'REG-' . date('Y') . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT),
            'roll_number' => 'R-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'],
            'category' => $data['category'],
            'blood_group' => $data['blood_group'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'admission_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        if (! empty($data['guardian_name'])) {
            StudentGuardian::create([
                'student_id' => $student->id,
                'relation' => 'Father',
                'name' => $data['guardian_name'],
                'phone' => $data['guardian_phone'] ?? $data['phone'],
            ]);
        }

        return back()->with('success', "Student {$student->full_name} ({$student->unique_student_id}) enrolled.");
    }

    public function show(Student $student): View
    {
        $student->load([
            'college',
            'department',
            'course',
            'classSection',
            'guardians',
            'documents',
            'feeAccounts.payments',
            'examMarks.subject',
            'certificates',
            'attendances',
        ]);

        return view('students.show', compact('student'));
    }

    public function updateStatus(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:Active,Passed Out,Suspended,Withdrawn',
            'current_semester' => 'nullable|integer|min:1|max:10',
            'transfer_migration_notes' => 'nullable|string',
        ]);

        $student->update([
            'status' => $data['status'],
            'current_semester' => $data['current_semester'] ?? $student->current_semester,
            'transfer_migration_notes' => $data['transfer_migration_notes'] ?? $student->transfer_migration_notes,
        ]);

        return back()->with('success', 'Student status & academic record updated.');
    }

    public function uploadDocument(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'document_type' => 'required|string|max:80',
            'document_file' => 'required|file|max:5120',
        ]);

        $path = $request->file('document_file')->store('students/documents', 'public');
        StudentDocument::create([
            'student_id' => $student->id,
            'document_type' => $request->document_type,
            'file_path' => $path,
            'is_verified' => true,
        ]);

        return back()->with('success', 'Student document uploaded and verified.');
    }

    public function idCard(Student $student): View
    {
        $student->load(['college.campus', 'department', 'course']);

        return view('students.id_card', compact('student'));
    }
}

