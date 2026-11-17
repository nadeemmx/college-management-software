@extends('layouts.app')
@section('title', 'Student Portal')
@section('header', 'Student Self-Service Portal')

@section('content')
@if($student)
<div class="grid md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Student ID</div><div class="text-lg font-bold text-indigo-700">{{ $student->unique_student_id }}</div><div class="text-[11px] text-slate-500">{{ $student->course->code }} &bull; Sem {{ $student->current_semester }}</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Attendance</div><div class="text-2xl font-bold {{ $attendancePct < 75 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $attendancePct }}%</div><div class="text-[11px] text-slate-400">Min 75% required</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Fee Balance</div><div class="text-2xl font-bold text-slate-900">₹{{ number_format($student->feeAccounts->sum('balance_amount')) }}</div><div class="text-[11px] text-slate-400">Current Session</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col justify-between">
        <div class="text-xs text-slate-500">Quick Downloads</div>
        <div class="flex gap-2 mt-2 text-xs">
            <a href="{{ route('students.id_card', $student) }}" target="_blank" class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 font-medium">ID Card</a>
            <a href="{{ route('exams.marksheet', $student) }}" target="_blank" class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 font-medium">Marksheet</a>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">My Weekly Timetable</h3>
        <div class="space-y-2 text-xs">
            @foreach($timetable as $slot)
                <div class="p-2.5 rounded bg-slate-50 border border-slate-200 flex justify-between">
                    <span><strong>{{ $slot->day_of_week }}</strong> ({{ substr($slot->start_time,0,5) }}-{{ substr($slot->end_time,0,5) }}) &bull; {{ $slot->subject->name ?? '' }}</span>
                    <span class="text-indigo-700 font-medium">{{ $slot->room_no }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Submit LMS Assignment</h3>
        <form method="POST" action="{{ route('portal.student.submit') }}" class="space-y-2.5 text-xs">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <select name="lms_assignment_id" class="w-full rounded border border-slate-300 px-3 py-2">@foreach($assignments as $a)<option value="{{ $a->id }}">{{ $a->subject->code ?? '' }} - {{ $a->title }} (Due {{ $a->due_date }})</option>@endforeach</select>
            <input type="text" name="submission_url" placeholder="Submission Link / Drive / GitHub URL *" required class="w-full rounded border border-slate-300 px-3 py-2">
            <input type="text" name="submission_notes" placeholder="Notes..." class="w-full rounded border border-slate-300 px-3 py-2">
            <button class="w-full rounded-lg bg-indigo-600 text-white font-semibold py-2">Submit Assignment</button>
        </form>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 text-xs space-y-2">
        <h3 class="text-sm font-semibold text-slate-900 mb-2">Library Books &amp; Fee Receipts</h3>
        @foreach($libraryIssues as $li)
            <div class="p-2 rounded bg-slate-50 border flex justify-between"><span>{{ $li->book->title ?? '' }}</span><span class="font-semibold">{{ $li->status }} (Due: {{ $li->due_date }})</span></div>
        @endforeach
        @foreach($student->feeAccounts->flatMap->payments as $pay)
            <div class="p-2 rounded bg-emerald-50 border border-emerald-200 flex justify-between"><span>Receipt {{ $pay->receipt_no }} ({{ $pay->payment_mode }})</span><a href="{{ route('fees.receipt', $pay) }}" target="_blank" class="underline font-semibold">₹{{ number_format($pay->amount) }} Print</a></div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 text-xs space-y-2">
        <h3 class="text-sm font-semibold text-slate-900 mb-2">Notices &amp; Announcements</h3>
        @foreach($announcements as $ann)
            <div class="p-2.5 rounded bg-slate-50 border"><strong>{{ $ann->title }}</strong><div class="text-slate-600 mt-0.5">{{ $ann->message }}</div></div>
        @endforeach
    </div>
</div>
@endif
@endsection

