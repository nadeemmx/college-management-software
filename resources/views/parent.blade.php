@extends('layouts.app')
@section('title', 'Parent Portal')
@section('header', 'Parent / Guardian Portal')

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex items-center justify-between">
    <div class="text-sm font-semibold">Linked Ward / Child Switcher</div>
    <form method="GET" class="flex gap-2 text-xs">
        <select name="student_id" onchange="this.form.submit()" class="rounded border border-slate-300 px-3 py-1.5">
            @foreach($children as $child)
                <option value="{{ $child->id }}" @selected($selectedChild && $selectedChild->id === $child->id)>{{ $child->full_name }} ({{ $child->unique_student_id }})</option>
            @endforeach
        </select>
    </form>
</div>

@if($selectedChild)
<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Ward</div><div class="text-lg font-bold">{{ $selectedChild->full_name }}</div><div class="text-xs text-slate-500">{{ $selectedChild->course->name }} &bull; Sem {{ $selectedChild->current_semester }}</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Attendance Status</div><div class="text-2xl font-bold {{ $attendancePct < 75 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $attendancePct }}%</div></div>
    <div class="bg-white p-4 rounded-xl border border-slate-200"><div class="text-xs text-slate-500">Outstanding Fees</div><div class="text-2xl font-bold text-rose-600">₹{{ number_format($selectedChild->feeAccounts->sum('balance_amount')) }}</div></div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 text-xs space-y-2">
        <h3 class="text-sm font-semibold text-slate-900 mb-2">Recent Exam Results</h3>
        @foreach($selectedChild->examMarks as $m)
            <div class="p-2.5 rounded bg-slate-50 border flex justify-between">
                <span>{{ $m->subject->code ?? '' }} - {{ $m->subject->name ?? '' }}</span>
                <span class="font-bold text-indigo-700">{{ $m->total_marks }}/100 ({{ $m->grade }})</span>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 text-xs space-y-2">
        <h3 class="text-sm font-semibold text-slate-900 mb-2">College Notices &amp; Emergency Alerts</h3>
        @foreach($announcements as $ann)
            <div class="p-2.5 rounded bg-slate-50 border"><strong>{{ $ann->title }}</strong><div class="text-slate-600 mt-0.5">{{ $ann->message }}</div></div>
        @endforeach
    </div>
</div>
@endif
@endsection

