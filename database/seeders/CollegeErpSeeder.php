<?php

namespace Database\Seeders;

use App\Models\AcademicCalendar;
use App\Models\AcademicSession;
use App\Models\AcademicYear;
use App\Models\AccreditationRecord;
use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use App\Models\Alumnus;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Campus;
use App\Models\CampusEvent;
use App\Models\Certificate;
use App\Models\ChartOfAccount;
use App\Models\ClassSection;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\DepartmentBudget;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeLeave;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamRegistration;
use App\Models\ExamSchedule;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\FinancialVoucher;
use App\Models\FinancialYear;
use App\Models\FixedAsset;
use App\Models\Grievance;
use App\Models\Hostel;
use App\Models\HostelActivity;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\InventoryItem;
use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\LmsAssignment;
use App\Models\LmsDiscussion;
use App\Models\LmsMaterial;
use App\Models\LmsSubmission;
use App\Models\NotificationLog;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\PlacementApplication;
use App\Models\PlacementCompany;
use App\Models\PlacementDrive;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\ResearchRecord;
use App\Models\Role;
use App\Models\SalaryStructure;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeAccount;
use App\Models\StudentGuardian;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Models\SystemSetting;
use App\Models\TimetableSlot;
use App\Models\TransportAllocation;
use App\Models\TransportRoute;
use App\Models\TransportVehicle;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CollegeErpSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Campuses & Colleges
        $mainCampus = Campus::create([
            'code' => 'BLR-01',
            'name' => 'WebotApp Main Campus',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'address' => '120 Knowledge Park, Yelahanka, Bengaluru - 560064',
            'phone' => '+91-80-28460000',
            'email' => 'info@webotapp.edu.in',
        ]);

        $northCampus = Campus::create([
            'code' => 'PUN-02',
            'name' => 'WebotApp Pune Campus',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'address' => '45 Hinjewadi Phase 2, Pune - 411057',
            'phone' => '+91-20-22930000',
            'email' => 'pune@webotapp.edu.in',
        ]);

        $collegeTech = College::create([
            'campus_id' => $mainCampus->id,
            'code' => 'WAIT',
            'name' => 'WebotApp Institute of Technology',
            'university_affiliation' => 'Visvesvaraya Technological University (VTU)',
            'aishe_code' => 'C-12345',
            'naac_grade' => 'A+',
            'principal_name' => 'Dr. Raghavendra Rao',
        ]);

        $collegeArts = College::create([
            'campus_id' => $northCampus->id,
            'code' => 'WACAC',
            'name' => 'WebotApp College of Arts & Commerce',
            'university_affiliation' => 'Savitribai Phule Pune University (SPPU)',
            'aishe_code' => 'C-54321',
            'naac_grade' => 'A',
            'principal_name' => 'Dr. Meenakshi Deshmukh',
        ]);

        // 2. Academic & Financial Years
        $ay = AcademicYear::create([
            'name' => '2026-27',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ]);

        FinancialYear::create([
            'name' => 'FY 2026-27',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $session = AcademicSession::create([
            'academic_year_id' => $ay->id,
            'name' => '2026-27 Odd Semester',
            'term' => 'Odd',
            'start_date' => '2026-07-15',
            'end_date' => '2026-12-20',
            'is_active' => true,
        ]);

        // 3. Roles & Permissions
        $roles = [];
        foreach ([
            ['super_admin', 'Super Administrator', 'Full ERP access across all campuses and modules'],
            ['faculty', 'Faculty / Professor', 'Teaching, attendance, marks entry, LMS and leave'],
            ['student', 'Student', 'Student portal, attendance, results, fees and LMS'],
            ['parent', 'Parent / Guardian', 'Parent portal for linked children tracking'],
            ['accountant', 'Finance & Accounts', 'Fees, vouchers, payroll and procurement'],
            ['librarian', 'Librarian', 'Library catalog, issue/return and stock verification'],
        ] as [$slug, $name, $desc]) {
            $roles[$slug] = Role::create(['slug' => $slug, 'name' => $name, 'description' => $desc]);
        }

        foreach (['admissions', 'students', 'academics', 'attendance', 'exams', 'library', 'fees', 'finance', 'hr', 'payroll', 'hostel', 'transport', 'lms', 'placement', 'compliance', 'settings'] as $mod) {
            $perm = Permission::create(['slug' => "manage.{$mod}", 'name' => 'Manage ' . ucfirst($mod), 'module' => ucfirst($mod)]);
            $roles['super_admin']->permissions()->attach($perm->id);
        }

        // 4. Demo Users
        $adminUser = User::create([
            'name' => 'Dr. Raghavendra Rao (Admin)',
            'email' => 'admin@webotapp.edu.in',
            'password' => $password,
            'role_id' => $roles['super_admin']->id,
            'campus_id' => $mainCampus->id,
            'college_id' => $collegeTech->id,
            'phone' => '9845012345',
        ]);

        $facultyUser = User::create([
            'name' => 'Prof. Ananya Sharma',
            'email' => 'faculty@webotapp.edu.in',
            'password' => $password,
            'role_id' => $roles['faculty']->id,
            'campus_id' => $mainCampus->id,
            'college_id' => $collegeTech->id,
            'phone' => '9845022222',
        ]);

        $studentUser = User::create([
            'name' => 'Aarav Kulkarni',
            'email' => 'student@webotapp.edu.in',
            'password' => $password,
            'role_id' => $roles['student']->id,
            'campus_id' => $mainCampus->id,
            'college_id' => $collegeTech->id,
            'phone' => '9900112233',
        ]);

        $parentUser = User::create([
            'name' => 'Suresh Kulkarni',
            'email' => 'parent@webotapp.edu.in',
            'password' => $password,
            'role_id' => $roles['parent']->id,
            'campus_id' => $mainCampus->id,
            'college_id' => $collegeTech->id,
            'phone' => '9845099887',
        ]);

        // 5. Departments & Courses
        $deptCse = Department::create(['college_id' => $collegeTech->id, 'code' => 'CSE', 'name' => 'Computer Science & Engineering', 'hod_name' => 'Dr. Vikramaditya Nair']);
        $deptEce = Department::create(['college_id' => $collegeTech->id, 'code' => 'ECE', 'name' => 'Electronics & Communication', 'hod_name' => 'Dr. Kavita Iyer']);
        $deptCom = Department::create(['college_id' => $collegeArts->id, 'code' => 'COM', 'name' => 'Commerce & Management', 'hod_name' => 'Prof. Rajeshwari Joshi']);

        $courseBtech = Course::create([
            'department_id' => $deptCse->id,
            'code' => 'BTECH-CSE',
            'name' => 'B.Tech Computer Science & Engineering',
            'level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credits' => 160,
            'intake_capacity' => 120,
            'cutoff_percentage' => 75.00,
        ]);

        $courseMtech = Course::create([
            'department_id' => $deptCse->id,
            'code' => 'MTECH-AI',
            'name' => 'M.Tech Artificial Intelligence & Data Science',
            'level' => 'Postgraduate',
            'duration_years' => 2,
            'total_semesters' => 4,
            'total_credits' => 80,
            'intake_capacity' => 30,
            'cutoff_percentage' => 70.00,
        ]);

        $courseBcom = Course::create([
            'department_id' => $deptCom->id,
            'code' => 'BCOM-HONS',
            'name' => 'B.Com (Honours) Accounting & Finance',
            'level' => 'Undergraduate',
            'duration_years' => 3,
            'total_semesters' => 6,
            'total_credits' => 120,
            'intake_capacity' => 90,
            'cutoff_percentage' => 60.00,
        ]);

        $courseDiploma = Course::create([
            'department_id' => $deptEce->id,
            'code' => 'DIP-IOT',
            'name' => 'Diploma in Embedded Systems & IoT',
            'level' => 'Diploma',
            'duration_years' => 2,
            'total_semesters' => 4,
            'total_credits' => 80,
            'intake_capacity' => 60,
            'cutoff_percentage' => 50.00,
        ]);

        Course::create([
            'department_id' => $deptCse->id,
            'code' => 'CERT-FSWD',
            'name' => 'Certificate in Full-Stack Web Development',
            'level' => 'Certificate',
            'duration_years' => 1,
            'total_semesters' => 2,
            'total_credits' => 24,
            'intake_capacity' => 40,
            'cutoff_percentage' => 45.00,
        ]);

        // 6. Subjects & Academic Calendar
        $subDsa = Subject::create(['course_id' => $courseBtech->id, 'code' => 'CS301', 'name' => 'Data Structures & Algorithms', 'semester' => 3, 'credits' => 4.0, 'subject_type' => 'Core', 'max_internal_marks' => 30, 'max_external_marks' => 70]);
        $subDbms = Subject::create(['course_id' => $courseBtech->id, 'code' => 'CS302', 'name' => 'Database Management Systems', 'semester' => 3, 'credits' => 4.0, 'subject_type' => 'Core', 'max_internal_marks' => 30, 'max_external_marks' => 70]);
        $subOs = Subject::create(['course_id' => $courseBtech->id, 'code' => 'CS303', 'name' => 'Operating Systems', 'semester' => 3, 'credits' => 3.0, 'subject_type' => 'Core', 'max_internal_marks' => 30, 'max_external_marks' => 70]);
        $subIks = Subject::create(['course_id' => $courseBtech->id, 'code' => 'NEP304', 'name' => 'Indian Knowledge Systems (NEP)', 'semester' => 3, 'credits' => 2.0, 'subject_type' => 'AECC', 'max_internal_marks' => 40, 'max_external_marks' => 60]);
        $subLab = Subject::create(['course_id' => $courseBtech->id, 'code' => 'CS305L', 'name' => 'DBMS & DSA Laboratory', 'semester' => 3, 'credits' => 2.0, 'subject_type' => 'Practical', 'max_internal_marks' => 50, 'max_external_marks' => 50]);

        AcademicCalendar::create(['academic_session_id' => $session->id, 'title' => 'Commencement of Odd Semester Classes', 'event_type' => 'Academic', 'start_date' => '2026-07-15']);
        AcademicCalendar::create(['academic_session_id' => $session->id, 'title' => 'Independence Day Celebration', 'event_type' => 'Holiday', 'start_date' => '2026-08-15']);
        AcademicCalendar::create(['academic_session_id' => $session->id, 'title' => 'Mid-Semester Examinations', 'event_type' => 'Examination', 'start_date' => '2026-10-05', 'end_date' => '2026-10-12']);
        AcademicCalendar::create(['academic_session_id' => $session->id, 'title' => 'End-Semester Theory Examinations', 'event_type' => 'Examination', 'start_date' => '2026-11-25', 'end_date' => '2026-12-10']);

        // 7. Faculty & Staff + Salary & Payroll
        $fac1 = Employee::create([
            'user_id' => $facultyUser->id,
            'college_id' => $collegeTech->id,
            'department_id' => $deptCse->id,
            'staff_id' => 'FAC-101',
            'full_name' => 'Prof. Ananya Sharma',
            'email' => 'faculty@webotapp.edu.in',
            'phone' => '9845022222',
            'employee_type' => 'Faculty',
            'designation' => 'Associate Professor',
            'qualification' => 'Ph.D. (IISc Bengaluru)',
            'experience_years' => 9,
            'specialization' => 'Database Systems & AI',
            'joining_date' => '2018-06-01',
            'basic_salary' => 68000,
            'bank_account_no' => '30124567890',
            'ifsc_code' => 'SBIN0004321',
            'pan_number' => 'ABCDE1234F',
            'uan_number' => '100987654321',
        ]);

        $fac2 = Employee::create([
            'college_id' => $collegeTech->id,
            'department_id' => $deptCse->id,
            'staff_id' => 'FAC-102',
            'full_name' => 'Dr. Vikramaditya Nair',
            'email' => 'vikram.nair@webotapp.edu.in',
            'phone' => '9845033333',
            'employee_type' => 'Faculty',
            'designation' => 'Professor & HOD',
            'qualification' => 'Ph.D. (IIT Madras)',
            'experience_years' => 15,
            'specialization' => 'Algorithms & Distributed Systems',
            'joining_date' => '2014-07-10',
            'basic_salary' => 95000,
        ]);

        $fac3 = Employee::create([
            'college_id' => $collegeTech->id,
            'department_id' => $deptEce->id,
            'staff_id' => 'FAC-103',
            'full_name' => 'Dr. Kavita Iyer',
            'email' => 'kavita.iyer@webotapp.edu.in',
            'phone' => '9845044444',
            'employee_type' => 'Faculty',
            'designation' => 'Professor',
            'qualification' => 'Ph.D. (NIT Surathkal)',
            'experience_years' => 12,
            'specialization' => 'VLSI & Embedded IoT',
            'joining_date' => '2016-08-01',
            'basic_salary' => 85000,
        ]);

        $staff1 = Employee::create([
            'college_id' => $collegeTech->id,
            'department_id' => $deptCse->id,
            'staff_id' => 'STF-201',
            'full_name' => 'Mahesh Hegde',
            'email' => 'mahesh.hegde@webotapp.edu.in',
            'phone' => '9845055555',
            'employee_type' => 'Non-Teaching',
            'designation' => 'Chief Warden & Lab Superintendent',
            'qualification' => 'M.Sc. Electronics',
            'experience_years' => 8,
            'joining_date' => '2019-01-15',
            'basic_salary' => 38000,
        ]);

        foreach ([$fac1, $fac2, $fac3, $staff1] as $emp) {
            $hra = round($emp->basic_salary * 0.20, 2);
            $da = round($emp->basic_salary * 0.15, 2);
            $ta = 3200;
            $gross = $emp->basic_salary + $hra + $da + $ta;
            $pf = round($emp->basic_salary * 0.12, 2);
            $pt = 200;
            $tds = $gross > 80000 ? 4500 : 1200;

            SalaryStructure::create([
                'employee_id' => $emp->id,
                'basic_salary' => $emp->basic_salary,
                'hra' => $hra,
                'da' => $da,
                'ta' => $ta,
                'special_allowance' => 0,
                'pf_enabled' => true,
                'esi_enabled' => $gross <= 21000,
                'pt_enabled' => true,
                'tds_monthly' => $tds,
            ]);

            Payroll::create([
                'employee_id' => $emp->id,
                'month' => 10,
                'year' => 2026,
                'working_days' => 26,
                'leave_deduction_days' => 0,
                'gross_salary' => $gross,
                'pf_amount' => $pf,
                'esi_amount' => 0,
                'professional_tax' => $pt,
                'tds_amount' => $tds,
                'net_salary' => $gross - $pf - $pt - $tds,
                'status' => 'Paid',
                'paid_at' => '2026-10-31',
                'bank_ref' => 'NEFT-OCT26-' . $emp->id . '99',
            ]);

            EmployeeAttendance::create([
                'employee_id' => $emp->id,
                'attendance_date' => now()->toDateString(),
                'check_in' => '09:05:00',
                'check_out' => '17:15:00',
                'status' => 'Present',
                'method' => 'Biometric',
            ]);
        }

        EmployeeLeave::create([
            'employee_id' => $fac1->id,
            'leave_type' => 'Casual',
            'from_date' => now()->addDays(5)->toDateString(),
            'to_date' => now()->addDays(6)->toDateString(),
            'days' => 2,
            'reason' => 'Attending IEEE Conference in Hyderabad',
            'status' => 'Pending',
        ]);

        // 8. Class Sections, Subject Allocations & Timetable
        $secA = ClassSection::create(['course_id' => $courseBtech->id, 'semester' => 3, 'section_name' => 'A', 'room_no' => 'CS-301', 'class_teacher_id' => $fac1->id]);
        $secB = ClassSection::create(['course_id' => $courseBcom->id, 'semester' => 1, 'section_name' => 'A', 'room_no' => 'CM-101', 'class_teacher_id' => $fac2->id]);

        SubjectAllocation::create(['academic_session_id' => $session->id, 'class_section_id' => $secA->id, 'subject_id' => $subDsa->id, 'faculty_id' => $fac2->id]);
        SubjectAllocation::create(['academic_session_id' => $session->id, 'class_section_id' => $secA->id, 'subject_id' => $subDbms->id, 'faculty_id' => $fac1->id]);
        SubjectAllocation::create(['academic_session_id' => $session->id, 'class_section_id' => $secA->id, 'subject_id' => $subLab->id, 'faculty_id' => $fac1->id]);

        TimetableSlot::create(['class_section_id' => $secA->id, 'subject_id' => $subDsa->id, 'faculty_id' => $fac2->id, 'day_of_week' => 'Monday', 'start_time' => '09:30:00', 'end_time' => '10:30:00', 'room_no' => 'CS-301', 'slot_type' => 'Lecture']);
        TimetableSlot::create(['class_section_id' => $secA->id, 'subject_id' => $subDbms->id, 'faculty_id' => $fac1->id, 'day_of_week' => 'Monday', 'start_time' => '10:45:00', 'end_time' => '11:45:00', 'room_no' => 'CS-301', 'slot_type' => 'Lecture']);
        TimetableSlot::create(['class_section_id' => $secA->id, 'subject_id' => $subLab->id, 'faculty_id' => $fac1->id, 'day_of_week' => 'Tuesday', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'room_no' => 'LAB-02', 'slot_type' => 'Lab']);
        TimetableSlot::create(['class_section_id' => $secA->id, 'subject_id' => $subOs->id, 'faculty_id' => $fac3->id, 'day_of_week' => 'Wednesday', 'start_time' => '09:30:00', 'end_time' => '10:30:00', 'room_no' => 'CS-301', 'slot_type' => 'Lecture']);

        // 9. Students & Guardians
        $studentsData = [
            ['Aarav Kulkarni', 'student@webotapp.edu.in', '9900112233', 'Male', 'General', 'WA26CS001', 'ADM-2026-001', '1WA25CS001', $studentUser->id],
            ['Priya Deshpande', 'priya.d@webotapp.edu.in', '9900112234', 'Female', 'EWS', 'WA26CS002', 'ADM-2026-002', '1WA25CS002', null],
            ['Rohan Verma', 'rohan.v@webotapp.edu.in', '9900112235', 'Male', 'OBC', 'WA26CS003', 'ADM-2026-003', '1WA25CS003', null],
            ['Sneha Patil', 'sneha.p@webotapp.edu.in', '9900112236', 'Female', 'SC', 'WA26CS004', 'ADM-2026-004', '1WA25CS004', null],
            ['Karthik Reddy', 'karthik.r@webotapp.edu.in', '9900112237', 'Male', 'General', 'WA26CS005', 'ADM-2026-005', '1WA25CS005', null],
            ['Anjali Kumari', 'anjali.k@webotapp.edu.in', '9900112238', 'Female', 'ST', 'WA26CS006', 'ADM-2026-006', '1WA25CS006', null],
        ];

        $students = [];
        foreach ($studentsData as $idx => $sd) {
            $stu = Student::create([
                'user_id' => $sd[8],
                'college_id' => $collegeTech->id,
                'department_id' => $deptCse->id,
                'course_id' => $courseBtech->id,
                'current_semester' => 3,
                'class_section_id' => $secA->id,
                'unique_student_id' => $sd[5],
                'admission_number' => $sd[6],
                'registration_number' => $sd[7],
                'roll_number' => 'CS-0' . ($idx + 1),
                'full_name' => $sd[0],
                'email' => $sd[1],
                'phone' => $sd[2],
                'dob' => '2006-05-1' . ($idx + 1),
                'gender' => $sd[3],
                'blood_group' => 'O+',
                'category' => $sd[4],
                'aadhaar_last4' => '432' . $idx,
                'permanent_address' => 'Bengaluru, Karnataka',
                'emergency_contact_name' => 'Suresh ' . explode(' ', $sd[0])[1] ?? 'Parent',
                'emergency_contact_phone' => '984509988' . $idx,
                'admission_date' => '2025-08-01',
                'status' => 'Active',
            ]);
            $students[] = $stu;

            StudentGuardian::create([
                'student_id' => $stu->id,
                'parent_user_id' => $idx === 0 ? $parentUser->id : null,
                'relation' => 'Father',
                'name' => $idx === 0 ? 'Suresh Kulkarni' : 'Mr. ' . $sd[0] . ' Sr.',
                'phone' => '984509988' . $idx,
                'email' => $idx === 0 ? 'parent@webotapp.edu.in' : null,
                'occupation' => 'Engineer',
                'annual_income' => 850000,
            ]);
        }

        // Student Attendance (make student[2] have low attendance < 75%)
        for ($d = 0; $d < 10; $d++) {
            $date = now()->subDays($d)->toDateString();
            foreach ($students as $idx => $stu) {
                $status = ($idx === 2 && $d < 5) ? 'Absent' : (($d === 0 && $idx === 1) ? 'Late' : 'Present');
                StudentAttendance::create([
                    'student_id' => $stu->id,
                    'subject_id' => $subDbms->id,
                    'class_section_id' => $secA->id,
                    'attendance_date' => $date,
                    'status' => $status,
                    'method' => $d % 2 === 0 ? 'QR' : 'Manual',
                    'marked_by' => $facultyUser->id,
                ]);
            }
        }

        // 10. Enquiries & Online Admission Applications
        AdmissionEnquiry::create([
            'enquiry_no' => 'ENQ-2026-001',
            'student_name' => 'Siddharth Malhotra',
            'phone' => '9811223344',
            'email' => 'siddharth@example.com',
            'source' => 'Online',
            'campaign_name' => 'JEE Mains 2026 Drive',
            'course_id' => $courseBtech->id,
            'counsellor_id' => $adminUser->id,
            'status' => 'Follow-up',
            'next_followup_date' => now()->addDays(2)->toDateString(),
            'notes' => 'Interested in CSE with hostel facility.',
        ]);

        AdmissionEnquiry::create([
            'enquiry_no' => 'ENQ-2026-002',
            'student_name' => 'Meera Krishnan',
            'phone' => '9811223355',
            'source' => 'Walk-in',
            'course_id' => $courseMtech->id,
            'counsellor_id' => $adminUser->id,
            'status' => 'Converted',
            'notes' => 'GATE qualified, applied online.',
        ]);

        AdmissionApplication::create([
            'application_no' => 'APP-2026-1001',
            'academic_session_id' => $session->id,
            'course_id' => $courseBtech->id,
            'full_name' => 'Ishaan Chatterjee',
            'email' => 'ishaan.c@example.com',
            'phone' => '9876543210',
            'dob' => '2008-03-14',
            'gender' => 'Male',
            'category' => 'General',
            'aadhaar_number' => '123456789012',
            'aadhaar_consent' => true,
            'father_name' => 'Subhash Chatterjee',
            'class_10_board' => 'CBSE',
            'class_10_percentage' => 94.20,
            'class_12_board' => 'CBSE',
            'class_12_percentage' => 91.50,
            'entrance_exam_name' => 'KCET / JEE',
            'entrance_score' => 89.40,
            'merit_score' => 90.45,
            'application_fee_status' => 'Paid',
            'payment_txn_id' => 'RZP_TXN_998877',
            'status' => 'Verified',
        ]);

        AdmissionApplication::create([
            'application_no' => 'APP-2026-1002',
            'academic_session_id' => $session->id,
            'course_id' => $courseBtech->id,
            'full_name' => 'Neha Gupta',
            'email' => 'neha.g@example.com',
            'phone' => '9876543211',
            'dob' => '2008-07-21',
            'gender' => 'Female',
            'category' => 'EWS',
            'class_10_board' => 'ICSE',
            'class_10_percentage' => 92.00,
            'class_12_board' => 'ISC',
            'class_12_percentage' => 88.60,
            'entrance_exam_name' => 'JEE Main',
            'entrance_score' => 85.00,
            'merit_score' => 86.80,
            'application_fee_status' => 'Paid',
            'payment_txn_id' => 'RZP_TXN_998878',
            'status' => 'Submitted',
        ]);

        // 11. Examinations & Marks
        $exam = Exam::create([
            'name' => 'Odd Semester End-Term Exam Nov 2026',
            'exam_type' => 'End-Semester',
            'academic_session_id' => $session->id,
            'course_id' => $courseBtech->id,
            'semester' => 3,
            'start_date' => '2026-11-25',
            'end_date' => '2026-12-05',
            'is_published' => true,
        ]);

        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subDsa->id, 'exam_date' => '2026-11-25', 'start_time' => '10:00:00', 'end_time' => '13:00:00', 'room_no' => 'HALL-A', 'invigilator_id' => $fac2->id]);
        ExamSchedule::create(['exam_id' => $exam->id, 'subject_id' => $subDbms->id, 'exam_date' => '2026-11-27', 'start_time' => '10:00:00', 'end_time' => '13:00:00', 'room_no' => 'HALL-A', 'invigilator_id' => $fac1->id]);

        foreach ($students as $idx => $stu) {
            ExamRegistration::create([
                'exam_id' => $exam->id,
                'student_id' => $stu->id,
                'seat_no' => 'SEAT-30' . ($idx + 1),
                'admit_card_number' => 'ADM-NOV26-00' . ($idx + 1),
                'is_eligible' => $idx !== 2,
            ]);

            foreach ([$subDsa, $subDbms, $subIks] as $sIdx => $subj) {
                $int = 24 + (($idx + $sIdx) % 5);
                $ext = 52 + (($idx * 3 + $sIdx * 4) % 18);
                $tot = $int + $ext;
                $gp = $tot >= 90 ? 10 : ($tot >= 80 ? 9 : ($tot >= 70 ? 8 : 7));
                $grade = $tot >= 90 ? 'O' : ($tot >= 80 ? 'A+' : ($tot >= 70 ? 'A' : 'B+'));
                ExamMark::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $subj->id,
                    'student_id' => $stu->id,
                    'internal_marks' => $int,
                    'external_marks' => $ext,
                    'total_marks' => $tot,
                    'grade' => $grade,
                    'grade_point' => $gp,
                    'credits' => $subj->credits,
                    'result_status' => 'Pass',
                    'is_verified' => true,
                ]);
            }
        }

        // 12. Library Books & Issues
        $book1 = LibraryBook::create(['isbn' => '978-0262046305', 'accession_no' => 'ACC-1001', 'barcode' => 'LIB1001', 'title' => 'Introduction to Algorithms (CLRS)', 'author' => 'Thomas H. Cormen', 'publisher' => 'MIT Press', 'subject_category' => 'Computer Science', 'rack_location' => 'R1-A2', 'total_copies' => 10, 'available_copies' => 8]);
        $book2 = LibraryBook::create(['isbn' => '978-0078022159', 'accession_no' => 'ACC-1002', 'barcode' => 'LIB1002', 'title' => 'Database System Concepts', 'author' => 'Silberschatz, Korth', 'publisher' => 'McGraw Hill', 'subject_category' => 'Database', 'rack_location' => 'R1-B4', 'total_copies' => 8, 'available_copies' => 7]);
        LibraryBook::create(['isbn' => '978-0134685991', 'accession_no' => 'ACC-1003', 'barcode' => 'LIB1003', 'title' => 'Effective Java', 'author' => 'Joshua Bloch', 'publisher' => 'Addison-Wesley', 'subject_category' => 'Programming', 'rack_location' => 'R2-C1', 'book_type' => 'E-Book', 'ebook_url' => 'https://library.webotapp.edu.in/ebooks/effective-java.pdf', 'total_copies' => 1, 'available_copies' => 1]);

        LibraryIssue::create([
            'library_book_id' => $book1->id,
            'user_id' => $studentUser->id,
            'member_type' => 'Student',
            'issue_date' => now()->subDays(20)->toDateString(),
            'due_date' => now()->subDays(6)->toDateString(),
            'status' => 'Overdue',
            'fine_amount' => 30.00,
        ]);

        // 13. Fees & Student Accounts + College Finance
        $feeStruct = FeeStructure::create([
            'course_id' => $courseBtech->id,
            'semester' => 3,
            'category' => 'All',
            'admission_fee' => 0,
            'tuition_fee' => 55000,
            'exam_fee' => 2500,
            'library_fee' => 1500,
            'lab_fee' => 4000,
            'misc_fee' => 2000,
            'total_fee' => 65000,
        ]);

        foreach ($students as $idx => $stu) {
            $paid = $idx < 3 ? 65000 : 35000;
            $acc = StudentFeeAccount::create([
                'student_id' => $stu->id,
                'fee_structure_id' => $feeStruct->id,
                'academic_session_id' => $session->id,
                'total_amount' => 65000,
                'concession_amount' => $stu->category === 'SC' ? 15000 : 0,
                'paid_amount' => $paid,
                'balance_amount' => max(0, 65000 - ($stu->category === 'SC' ? 15000 : 0) - $paid),
                'due_date' => '2026-11-30',
                'status' => $paid >= 65000 ? 'Paid' : 'Partial',
            ]);

            FeePayment::create([
                'student_fee_account_id' => $acc->id,
                'student_id' => $stu->id,
                'receipt_no' => 'RCPT-2026-00' . ($idx + 1),
                'amount' => $paid,
                'payment_mode' => $idx % 2 === 0 ? 'UPI' : 'NetBanking',
                'transaction_ref' => 'UPI' . rand(10000000, 99999999),
                'payment_date' => now()->subDays(15 - $idx)->toDateString(),
                'status' => 'Completed',
            ]);
        }

        $coaBank = ChartOfAccount::create(['code' => '1002', 'name' => 'SBI Main College Account', 'account_type' => 'Asset', 'balance' => 4850000]);
        $coaCash = ChartOfAccount::create(['code' => '1001', 'name' => 'Cash in Hand', 'account_type' => 'Asset', 'balance' => 125000]);
        $coaTuition = ChartOfAccount::create(['code' => '4001', 'name' => 'Tuition Fee Income', 'account_type' => 'Income', 'balance' => 3200000]);
        $coaSalary = ChartOfAccount::create(['code' => '5001', 'name' => 'Faculty & Staff Salaries', 'account_type' => 'Expense', 'balance' => 1850000]);
        $coaLabExp = ChartOfAccount::create(['code' => '5002', 'name' => 'Laboratory Consumables & Maintenance', 'account_type' => 'Expense', 'balance' => 240000]);

        FinancialVoucher::create([
            'voucher_no' => 'VCH-2026-001',
            'voucher_type' => 'Receipt',
            'voucher_date' => now()->subDays(5)->toDateString(),
            'debit_account_id' => $coaBank->id,
            'credit_account_id' => $coaTuition->id,
            'amount' => 320000,
            'department_id' => $deptCse->id,
            'narration' => 'B.Tech Sem 3 Tuition Fee Batch Settlement',
            'created_by' => $adminUser->id,
        ]);

        FinancialVoucher::create([
            'voucher_no' => 'VCH-2026-002',
            'voucher_type' => 'Payment',
            'voucher_date' => now()->subDays(2)->toDateString(),
            'debit_account_id' => $coaLabExp->id,
            'credit_account_id' => $coaBank->id,
            'amount' => 45000,
            'gst_amount' => 8100,
            'tds_amount' => 900,
            'department_id' => $deptCse->id,
            'narration' => 'Networking Switches & Lab Maintenance',
            'created_by' => $adminUser->id,
        ]);

        DepartmentBudget::create(['department_id' => $deptCse->id, 'financial_year' => 'FY 2026-27', 'allocated_amount' => 1500000, 'utilized_amount' => 485000]);
        DepartmentBudget::create(['department_id' => $deptEce->id, 'financial_year' => 'FY 2026-27', 'allocated_amount' => 1200000, 'utilized_amount' => 310000]);

        FixedAsset::create(['asset_code' => 'FA-CSE-01', 'name' => 'HP ProLiant Campus Server', 'category' => 'IT Infrastructure', 'department_id' => $deptCse->id, 'purchase_date' => '2025-04-10', 'purchase_cost' => 450000, 'depreciation_rate' => 15, 'current_value' => 382500]);

        $vendor = Vendor::create(['name' => 'TechBridge India Pvt Ltd', 'gstin' => '29AABCT1234F1Z5', 'pan' => 'AABCT1234F', 'contact_person' => 'Arun Kumar', 'phone' => '9880011223', 'email' => 'sales@techbridge.in', 'category' => 'IT & Lab Equipment']);
        $req = PurchaseRequisition::create(['req_no' => 'PR-2026-001', 'department_id' => $deptCse->id, 'requested_by' => $facultyUser->id, 'item_description' => '30x Dell OptiPlex Workstations for AI Lab', 'quantity' => 30, 'estimated_cost' => 1650000, 'status' => 'PO Created']);
        PurchaseOrder::create(['po_no' => 'PO-2026-001', 'vendor_id' => $vendor->id, 'purchase_requisition_id' => $req->id, 'order_date' => now()->subDays(7)->toDateString(), 'total_amount' => 1620000, 'status' => 'Ordered']);
        InventoryItem::create(['item_code' => 'INV-LAB-01', 'name' => 'Digital Oscilloscope 100MHz', 'category' => 'Lab Equipment', 'department_id' => $deptEce->id, 'quantity' => 18, 'unit' => 'Units', 'reorder_level' => 5]);

        // 14. Hostel & Transport
        $hostelBoys = Hostel::create(['name' => 'Vivekananda Boys Hostel', 'type' => 'Boys', 'warden_id' => $staff1->id, 'total_rooms' => 60, 'capacity' => 180, 'monthly_fee' => 4500, 'mess_fee' => 3500]);
        Hostel::create(['name' => 'Kalpana Chawla Girls Hostel', 'type' => 'Girls', 'warden_id' => $fac1->id, 'total_rooms' => 50, 'capacity' => 150, 'monthly_fee' => 4500, 'mess_fee' => 3500]);

        $room101 = HostelRoom::create(['hostel_id' => $hostelBoys->id, 'floor' => '1st Floor', 'room_no' => 'B-101', 'capacity' => 3, 'occupied_beds' => 1, 'room_type' => 'Triple']);
        HostelAllocation::create(['hostel_id' => $hostelBoys->id, 'hostel_room_id' => $room101->id, 'student_id' => $students[0]->id, 'bed_no' => 'B1', 'check_in_date' => '2026-07-15']);
        HostelActivity::create(['hostel_id' => $hostelBoys->id, 'student_id' => $students[0]->id, 'activity_type' => 'Outing Request', 'title' => 'Weekend Home Visit', 'details' => 'Leaving Friday 5 PM, returning Sunday 8 PM.', 'status' => 'Approved']);

        $bus1 = TransportVehicle::create(['registration_no' => 'KA-01-F-4521', 'vehicle_type' => 'Bus', 'capacity' => 50, 'driver_name' => 'Ramesh Gowda', 'driver_phone' => '9845111222', 'driver_license' => 'KA01-20150045678', 'gps_device_id' => 'GPS-BUS-01', 'last_fuel_liters' => 120]);
        $route1 = TransportRoute::create(['route_name' => 'Route 1: Majestic - Hebbal - Campus', 'transport_vehicle_id' => $bus1->id, 'stops_json' => ['Majestic', 'Mekhri Circle', 'Hebbal', 'Yelahanka', 'Campus'], 'monthly_fee' => 1800]);
        TransportAllocation::create(['transport_route_id' => $route1->id, 'student_id' => $students[1]->id, 'pickup_stop' => 'Hebbal', 'start_date' => '2026-07-15']);

        // 15. LMS
        LmsMaterial::create(['subject_id' => $subDbms->id, 'faculty_id' => $fac1->id, 'title' => 'Unit 1: ER Model & Relational Algebra Notes', 'material_type' => 'PDF', 'file_url' => 'https://lms.webotapp.edu.in/dbms-unit1.pdf', 'description' => 'Comprehensive lecture notes with solved GATE questions.']);
        $assign = LmsAssignment::create(['subject_id' => $subDbms->id, 'faculty_id' => $fac1->id, 'title' => 'Assignment 1: Normalize College Schema to BCNF', 'description' => 'Submit SQL DDL and normalization steps.', 'due_date' => now()->addDays(7)->toDateString(), 'max_marks' => 20]);
        LmsSubmission::create(['lms_assignment_id' => $assign->id, 'student_id' => $students[0]->id, 'submission_url' => 'https://github.com/aarav/dbms-bcnf', 'submission_notes' => 'Completed all 5 relations.', 'submitted_at' => now(), 'marks_obtained' => 18, 'feedback' => 'Well structured ER diagram.']);
        LmsDiscussion::create(['subject_id' => $subDbms->id, 'user_id' => $studentUser->id, 'topic' => 'Difference between 3NF and BCNF', 'message' => 'Can someone share a practical example where 3NF is satisfied but BCNF fails?']);

        // 16. Placements
        $comp1 = PlacementCompany::create(['name' => 'Tata Consultancy Services (TCS)', 'industry' => 'IT Services', 'hr_name' => 'Divya Menon', 'hr_email' => 'campus@tcs.com', 'hr_phone' => '022-67789999']);
        $comp2 = PlacementCompany::create(['name' => 'Razorpay', 'industry' => 'FinTech', 'hr_name' => 'Karan Mehta', 'hr_email' => 'university@razorpay.com', 'hr_phone' => '080-45678900']);
        $drive1 = PlacementDrive::create(['placement_company_id' => $comp1->id, 'job_title' => 'Software Engineer (Digital)', 'package_lpa' => 7.50, 'eligibility_cgpa' => 6.50, 'drive_date' => now()->addDays(15)->toDateString(), 'drive_type' => 'Full-Time', 'status' => 'Upcoming']);
        $drive2 = PlacementDrive::create(['placement_company_id' => $comp2->id, 'job_title' => 'Backend Engineering Intern', 'package_lpa' => 14.00, 'eligibility_cgpa' => 7.50, 'drive_date' => now()->addDays(22)->toDateString(), 'drive_type' => 'Internship', 'status' => 'Upcoming']);
        PlacementApplication::create(['placement_drive_id' => $drive1->id, 'student_id' => $students[0]->id, 'aptitude_score' => 86, 'status' => 'Shortlisted']);
        PlacementApplication::create(['placement_drive_id' => $drive2->id, 'student_id' => $students[1]->id, 'aptitude_score' => 92, 'status' => 'Selected']);

        // 17. Scholarships, Certificates, Grievances, Events, Alumni
        $sch1 = Scholarship::create(['name' => 'NSP Post-Matric SC/ST Scholarship', 'scheme_type' => 'Government', 'amount' => 25000, 'eligibility_criteria' => 'SC/ST category with family income < 2.5 LPA', 'academic_session_id' => $session->id]);
        $sch2 = Scholarship::create(['name' => 'WebotApp Merit Excellence Award', 'scheme_type' => 'Merit', 'amount' => 15000, 'eligibility_criteria' => 'SGPA >= 9.0', 'academic_session_id' => $session->id]);
        ScholarshipApplication::create(['scholarship_id' => $sch1->id, 'student_id' => $students[3]->id, 'status' => 'Approved', 'disbursed_amount' => 25000, 'disbursed_at' => now()->subDays(10)->toDateString()]);
        ScholarshipApplication::create(['scholarship_id' => $sch2->id, 'student_id' => $students[0]->id, 'status' => 'Verified']);

        Certificate::create([
            'certificate_no' => 'CERT-BON-2026-001',
            'student_id' => $students[0]->id,
            'certificate_type' => 'Bonafide',
            'issue_date' => now()->toDateString(),
            'status' => 'Issued',
            'verification_token' => Str::random(32),
            'remarks' => 'Issued for Passport / Education Loan Verification',
        ]);

        Grievance::create([
            'ticket_no' => 'GRV-2026-001',
            'student_id' => $students[0]->id,
            'category' => 'Hostel',
            'is_confidential' => false,
            'subject' => 'Wi-Fi Speed Issue in Block B 1st Floor',
            'description' => 'Wi-Fi disconnects frequently after 9 PM in B-101.',
            'assigned_to' => $adminUser->id,
            'status' => 'Under Review',
        ]);

        CampusEvent::create(['title' => 'TechUtsav 2026 - National Hackathon', 'category' => 'Workshop', 'event_date' => now()->addDays(12)->toDateString(), 'venue' => 'Main Auditorium', 'budget' => 150000, 'department_id' => $deptCse->id, 'status' => 'Planned']);
        Alumnus::create(['full_name' => 'Nikhil Kamath (Batch 2021)', 'email' => 'nikhil@alumni.webotapp.edu.in', 'phone' => '9880098800', 'graduation_year' => 2021, 'course_id' => $courseBtech->id, 'current_company' => 'Google India', 'designation' => 'Senior Software Engineer', 'city' => 'Bengaluru', 'is_mentor' => true, 'donation_total' => 50000]);

        // 18. Accreditation (NAAC/NIRF/AISHE) & Research
        AccreditationRecord::create(['framework' => 'NAAC', 'cycle_year' => '2026-27', 'criterion' => 'Criterion 1: Curricular Aspects', 'metric_code' => '1.1.1', 'title' => 'NEP 2020 & CBCS Curriculum Implementation', 'score_or_value' => '3.65 / 4.00', 'department_id' => $deptCse->id, 'status' => 'Verified']);
        AccreditationRecord::create(['framework' => 'NIRF', 'cycle_year' => '2026', 'criterion' => 'TLR & Graduation Outcomes', 'metric_code' => 'NIRF-ENGG', 'title' => 'Faculty-Student Ratio & Placement Median Salary', 'score_or_value' => 'Rank Band 101-150', 'status' => 'Submitted']);
        AccreditationRecord::create(['framework' => 'AISHE', 'cycle_year' => '2025-26', 'criterion' => 'DCF-II Institutional Data', 'metric_code' => 'C-12345', 'title' => 'Annual AISHE Portal Data Upload', 'score_or_value' => 'Uploaded & Certified', 'status' => 'Approved']);

        ResearchRecord::create(['record_type' => 'Patent', 'title' => 'AI-Based Low-Cost Soil Moisture Sensor for Indian Farmers', 'lead_name' => 'Dr. Kavita Iyer', 'department_id' => $deptEce->id, 'patent_no' => 'IN-202641009812', 'year' => 2026, 'status' => 'Granted']);
        ResearchRecord::create(['record_type' => 'Research Grant', 'title' => 'DST-SERB Grant on Multilingual Indic LLM', 'lead_name' => 'Prof. Ananya Sharma', 'department_id' => $deptCse->id, 'funding_agency' => 'DST-SERB, Govt. of India', 'grant_amount' => 2400000, 'year' => 2026, 'status' => 'Ongoing']);

        // 19. Announcements, Notification Logs, Audit Logs & System Settings
        Announcement::create(['title' => 'Odd Semester End-Term Exam Timetable Published', 'message' => 'All B.Tech & B.Com students can download their Exam Timetable and Admit Cards from the Student Portal.', 'target_audience' => 'All', 'channel' => 'Portal', 'published_at' => now(), 'created_by' => $adminUser->id]);
        Announcement::create(['title' => 'Fee Payment Reminder: Odd Semester Due 30 Nov', 'message' => 'Students with pending tuition fees are requested to clear dues via UPI/NetBanking to avoid late penalty.', 'target_audience' => 'Students', 'channel' => 'WhatsApp', 'published_at' => now(), 'created_by' => $adminUser->id]);

        NotificationLog::create(['recipient_name' => 'Aarav Kulkarni', 'recipient_contact' => '9900112233', 'channel' => 'WhatsApp', 'subject' => 'Admit Card Ready', 'message' => 'Your Nov 2026 Admit Card is ready for download.', 'status' => 'Sent', 'sent_at' => now()]);
        AuditLog::create(['user_id' => $adminUser->id, 'action' => 'SEED_INITIALIZED', 'module' => 'System', 'description' => 'Initialized WebotApp Indian College Management Software with 30 modules.', 'ip_address' => '127.0.0.1']);

        SystemSetting::create(['group_name' => 'general', 'key' => 'institution_name', 'value' => 'WebotApp University Group']);
        SystemSetting::create(['group_name' => 'grading', 'key' => 'grading_scale', 'value' => json_encode(['O' => 10, 'A+' => 9, 'A' => 8, 'B+' => 7, 'B' => 6, 'C' => 5, 'F' => 0])]);
        SystemSetting::create(['group_name' => 'integrations', 'key' => 'payment_gateway', 'value' => 'Razorpay (UPI / Cards / NetBanking)']);
        SystemSetting::create(['group_name' => 'integrations', 'key' => 'whatsapp_provider', 'value' => 'Meta WhatsApp Business Cloud API']);
    }
}
