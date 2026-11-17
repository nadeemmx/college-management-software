@extends('layouts.app')
@section('title', 'Faculty Portal')
@section('header', 'Faculty Self-Service Portal')

@section('content')
@if($faculty)
<div class="grid md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Faculty ID</div><div class="text-lg font-bold text-indigo-700">{{ $faculty->staff_id }}</div><div class="text-[11px] text-slate-500">{{ $faculty->designation }}</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Department</div><div class="text-lg font-bold text-slate-900">{{ $faculty->department->code ?? 'All' }}</div><div class="text-[11px] text-slate-500">{{ $faculty->specialization }}</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Assigned Subjects</div><div class="text-2xl font-bold text-emerald-600">{{ $faculty->subjectAllocations->count() }}</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Weekly Workload</div><div class="text-2xl font-bold text-slate-900">{{ $faculty->weekly_workload_hours }} hrs</div></div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3 text-xs">
        <h3 class="text-sm font-semibold text-slate-900">My Teaching Timetable &amp; Quick Actions</h3>
        @foreach($timetable as $slot)
            <div class="p-2.5 rounded bg-slate-50 border flex justify-between">
                <span><strong>{{ $slot->day_of_week }}</strong> ({{ substr($slot->start_time,0,5) }}-{{ substr($slot->end_time,0,5) }}) &bull; {{ $slot->subject->name ?? '' }}</span>
                <span class="font-semibold text-indigo-700">{{ $slot->room_no }}</span>
            </div>
        @endforeach
        <div class="flex gap-2 pt-2">
            <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white font-semibold">Mark Attendance</a>
            <a href="{{ route('exams.index') }}" class="px-3 py-2 rounded-lg bg-emerald-600 text-white font-semibold">Enter Marks</a>
            <a href="{{ route('lms.index') }}" class="px-3 py-2 rounded-lg bg-slate-800 text-white font-semibold">Upload LMS Notes</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Apply for Leave</h3>
        <form method="POST" action="{{ route('portal.faculty.leave') }}" class="space-y-2.5 text-xs mb-4">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $faculty->id }}">
            <select name="leave_type" class="w-full rounded border border-slate-300 px-3 py-2"><option>Casual</option><option>Medical</option><option>Earned</option><option>Duty</option></select>
            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="from_date" required class="rounded border border-slate-300 px-3 py-2">
                <input type="date" name="to_date" required class="rounded border border-slate-300 px-3 py-2">
            </div>
            <input type="text" name="reason" placeholder="Reason *" required class="w-full rounded border border-slate-300 px-3 py-2">
            <button class="w-full rounded-lg bg-indigo-600 text-white font-semibold py-2">Submit Leave Request</button>
        </form>
        <div class="space-y-1.5 text-xs">
            @foreach($faculty->leaves as $lv)
                <div class="p-2 rounded bg-slate-50 border flex justify-between"><span>{{ $lv->leave_type }} ({{ $lv->from_date }} to {{ $lv->to_date }})</span><span class="font-semibold">{{ $lv->status }}</span></div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

