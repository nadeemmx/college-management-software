@extends('layouts.guest')
@section('title', 'Online Admission Portal')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
    <div class="border-b border-slate-200 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Online Admission Application Form (2026-27)</h1>
        <p class="text-sm text-slate-500 mt-1">NEP 2020 &amp; CBCS Programmes &bull; Application Fee: ₹500 (UPI / Card / NetBanking)</p>
    </div>

    <form method="POST" action="{{ route('admission.public.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Programme / Course *</label>
                <select name="course_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->level }} - Cut-off {{ $c->cutoff_percentage }}%)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Academic Session</label>
                <select name="academic_session_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Applicant Full Name *</label>
                <input type="text" name="full_name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Email Address *</label>
                <input type="email" name="email" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Mobile Number *</label>
                <input type="text" name="phone" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Gender *</label>
                <select name="gender" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option>Male</option><option>Female</option><option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Category &amp; Reservation *</label>
                <select name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option>General</option><option>OBC</option><option>SC</option><option>ST</option><option>EWS</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Father's Name</label>
                <input type="text" name="father_name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Mother's Name</label>
                <input type="text" name="mother_name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Aadhaar Details (Optional, with Consent)</h3>
            <div class="grid md:grid-cols-2 gap-4 items-center">
                <input type="text" name="aadhaar_number" maxlength="12" placeholder="12-digit Aadhaar Number (Optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <label class="flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="aadhaar_consent" value="1" class="rounded border-slate-300">
                    I voluntarily consent to share Aadhaar for student verification.
                </label>
            </div>
        </div>

        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Academic Qualifications &amp; Entrance Exam</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <input type="text" name="class_10_board" placeholder="Class 10 Board (e.g. CBSE)" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <input type="number" step="0.01" name="class_10_percentage" placeholder="Class 10 %" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <input type="text" name="class_12_board" placeholder="Class 12 Board" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <input type="number" step="0.01" name="class_12_percentage" placeholder="Class 12 %" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <input type="text" name="entrance_exam_name" placeholder="Entrance (JEE/KCET/CUET)" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <input type="number" step="0.01" name="entrance_score" placeholder="Entrance Score / Percentile" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Passport Photograph</label>
                <input type="file" name="photo" class="w-full text-xs">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Applicant Signature</label>
                <input type="file" name="signature" class="w-full text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Permanent Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <span class="text-xs text-slate-500">Payment Mode: Instant UPI / Razorpay Demo (₹500)</span>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700">Pay ₹500 &amp; Submit Application</button>
        </div>
    </form>
</div>
@endsection

