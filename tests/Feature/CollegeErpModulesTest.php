<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\CollegeErpSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollegeErpModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CollegeErpSeeder::class);
    }

    public function test_unauthenticated_users_cannot_access_any_system_routes(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/demo-login/admin')->assertStatus(404);

        $protectedRoutes = [
            '/dashboard',
            '/apply',
            '/track-application',
            '/admissions',
            '/students',
            '/academics',
            '/attendance',
            '/exams',
            '/library',
            '/fees',
            '/finance',
            '/hr',
            '/payroll',
            '/settings',
            '/portal/student',
            '/portal/faculty',
            '/portal/parent',
        ];

        foreach ($protectedRoutes as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_proper_login_and_role_based_access_control(): void
    {
        // Wrong password rejected
        $this->post('/login', [
            'email' => 'admin@webotapp.edu.in',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // Inactive user rejected
        $studentUser = User::where('email', 'student@webotapp.edu.in')->firstOrFail();
        $studentUser->update(['status' => 'Inactive']);
        $this->post('/login', [
            'email' => 'student@webotapp.edu.in',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // Restore active student & test proper login + RBAC
        $studentUser->update(['status' => 'Active']);
        $this->post('/login', [
            'email' => 'student@webotapp.edu.in',
            'password' => 'password',
        ])->assertRedirect('/portal/student');
        $this->assertAuthenticatedAs($studentUser);

        $this->get('/portal/student')->assertStatus(200);
        $this->get('/dashboard')->assertStatus(403);
        $this->get('/settings')->assertStatus(403);
        $this->get('/finance')->assertStatus(403);

        // Logout works
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        // Faculty login + RBAC
        $this->post('/login', [
            'email' => 'faculty@webotapp.edu.in',
            'password' => 'password',
        ])->assertRedirect('/portal/faculty');
        $this->get('/portal/faculty')->assertStatus(200);
        $this->get('/attendance')->assertStatus(200);
        $this->get('/settings')->assertStatus(403);

        $this->post('/logout');

        // Parent login + RBAC
        $this->post('/login', [
            'email' => 'parent@webotapp.edu.in',
            'password' => 'password',
        ])->assertRedirect('/portal/parent');
        $this->get('/portal/parent')->assertStatus(200);
        $this->get('/dashboard')->assertStatus(403);
    }

    public function test_all_admin_erp_module_pages_render_without_error(): void
    {
        $admin = User::where('email', 'admin@webotapp.edu.in')->firstOrFail();
        $this->actingAs($admin);

        $routes = [
            '/dashboard',
            '/apply',
            '/track-application?application_no=APP-2026-1001',
            '/admissions',
            '/enquiries',
            '/students',
            '/academics',
            '/timetable',
            '/attendance',
            '/exams',
            '/library',
            '/fees',
            '/finance',
            '/hr',
            '/payroll',
            '/procurement',
            '/hostel',
            '/transport',
            '/lms',
            '/placements',
            '/scholarships',
            '/certificates',
            '/grievances',
            '/events-alumni',
            '/accreditation-research',
            '/communication',
            '/settings',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200, "Failed rendering route: {$uri}");
        }
    }

    public function test_printable_documents_and_portals_render_without_error(): void
    {
        $admin = User::where('email', 'admin@webotapp.edu.in')->firstOrFail();
        $this->actingAs($admin);

        $student = Student::firstOrFail();
        $exam = Exam::firstOrFail();
        $app = AdmissionApplication::firstOrFail();
        $payment = FeePayment::firstOrFail();
        $payroll = Payroll::firstOrFail();
        $cert = Certificate::firstOrFail();

        $this->get("/students/{$student->id}")->assertStatus(200);
        $this->get("/students/{$student->id}/id-card")->assertStatus(200);
        $this->get("/students/{$student->id}/marksheet")->assertStatus(200);
        $this->get("/exams/{$exam->id}/admit-card/{$student->id}")->assertStatus(200);
        $this->get("/admissions/{$app->id}/receipt")->assertStatus(200);
        $this->get("/fees/payments/{$payment->id}/receipt")->assertStatus(200);
        $this->get("/payroll/{$payroll->id}/payslip")->assertStatus(200);
        $this->get("/certificates/{$cert->id}/print")->assertStatus(200);
        $this->get("/verify-certificate/{$cert->verification_token}")->assertStatus(200);

        $this->get('/portal/student')->assertStatus(200);
        $this->get('/portal/faculty')->assertStatus(200);
        $this->get('/portal/parent')->assertStatus(200);
    }

    public function test_core_workflows_work_end_to_end(): void
    {
        $admin = User::where('email', 'admin@webotapp.edu.in')->firstOrFail();
        $this->actingAs($admin);

        $course = \App\Models\Course::firstOrFail();

        $resp = $this->post('/apply', [
            'course_id' => $course->id,
            'full_name' => 'Test Applicant',
            'email' => 'applicant.test@example.com',
            'phone' => '9876500001',
            'gender' => 'Female',
            'category' => 'General',
            'class_12_percentage' => 93.5,
            'entrance_score' => 91.0,
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseHas('admission_applications', ['email' => 'applicant.test@example.com']);

        $this->post('/admissions/merit-list')->assertRedirect();

        $slot = \App\Models\TimetableSlot::firstOrFail();
        $conflictResp = $this->post('/timetable/slots', [
            'class_section_id' => $slot->class_section_id,
            'subject_id' => $slot->subject_id,
            'faculty_id' => $slot->faculty_id,
            'day_of_week' => $slot->day_of_week,
            'start_time' => substr($slot->start_time, 0, 5),
            'end_time' => substr($slot->end_time, 0, 5),
            'room_no' => $slot->room_no,
            'slot_type' => 'Lecture',
        ]);
        $conflictResp->assertSessionHasErrors('conflict');

        $feeAcc = \App\Models\StudentFeeAccount::where('balance_amount', '>', 0)->firstOrFail();
        $this->post("/fees/accounts/{$feeAcc->id}/collect", [
            'amount' => 5000,
            'payment_mode' => 'UPI',
        ])->assertRedirect();
        $this->assertDatabaseHas('fee_payments', ['student_fee_account_id' => $feeAcc->id, 'amount' => 5000]);
    }
}
