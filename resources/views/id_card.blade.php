@extends('layouts.app')
@section('title', 'Student ID Card')
@section('header', 'Student Identity Card')

@section('content')
<div class="max-w-sm mx-auto">
    <div class="no-print mb-4 text-right">
        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold">Print ID Card</button>
    </div>

    <div class="bg-white border-2 border-indigo-900 rounded-2xl overflow-hidden shadow-md">
        <div class="bg-indigo-900 text-white p-4 text-center">
            <div class="text-sm font-bold uppercase tracking-wide">{{ $student->college->name }}</div>
            <div class="text-[11px] text-amber-300">{{ $student->college->campus->name ?? '' }}</div>
        </div>
        <div class="p-5 text-center space-y-2">
            <div class="w-20 h-20 mx-auto rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center text-2xl font-bold border-2 border-indigo-300">
                {{ substr($student->full_name, 0, 1) }}
            </div>
            <div class="text-lg font-bold text-slate-900">{{ $student->full_name }}</div>
            <div class="text-xs font-semibold text-indigo-700">{{ $student->unique_student_id }} &bull; {{ $student->course->code }}</div>

            <div class="text-left text-xs space-y-1 bg-slate-50 p-3 rounded-lg border border-slate-200 mt-3">
                <div><strong>Adm No:</strong> {{ $student->admission_number }}</div>
                <div><strong>Reg No:</strong> {{ $student->registration_number }}</div>
                <div><strong>Department:</strong> {{ $student->department->name }}</div>
                <div><strong>Blood Group:</strong> {{ $student->blood_group ?? 'O+' }}</div>
                <div><strong>Emergency:</strong> {{ $student->emergency_contact_phone ?? $student->phone }}</div>
            </div>
        </div>
        <div class="bg-amber-400 text-slate-900 text-[11px] font-semibold text-center py-1.5">
            VALID FOR ACADEMIC SESSION 2026-27 &bull; PRINCIPAL
        </div>
    </div>
</div>
@endsection

