@extends('layouts.app')
@section('title', $certificate->certificate_type . ' Certificate')
@section('header', 'Digital Certificate')

@section('content')
<div class="max-w-3xl mx-auto bg-white border-4 border-double border-indigo-900 rounded-xl p-10">
    <div class="flex justify-between items-start border-b-2 border-indigo-900 pb-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-indigo-950 uppercase">{{ $certificate->student->college->name ?? 'WebotApp Institute of Technology' }}</h2>
            <p class="text-xs text-slate-600">{{ $certificate->student->college->university_affiliation ?? '' }} &bull; NAAC {{ $certificate->student->college->naac_grade ?? 'A+' }}</p>
        </div>
        <button onclick="window.print()" class="no-print px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-semibold">Print Certificate</button>
    </div>

    <div class="text-center mb-8">
        <span class="px-6 py-1.5 rounded-full bg-indigo-900 text-white text-sm font-bold uppercase tracking-widest">{{ $certificate->certificate_type }} CERTIFICATE</span>
        <div class="text-xs text-slate-500 mt-2">Certificate No: <strong>{{ $certificate->certificate_no }}</strong> &bull; Date: <strong>{{ $certificate->issue_date }}</strong></div>
    </div>

    <p class="text-base leading-relaxed text-slate-800 mb-10 text-justify">
        This is to certify that <strong>{{ $certificate->student->full_name }}</strong>, bearing Unique Student ID
        <strong>{{ $certificate->student->unique_student_id }}</strong> and University Registration Number
        <strong>{{ $certificate->student->registration_number }}</strong>, is a bonafide student of this institution pursuing
        <strong>{{ $certificate->student->course->name }}</strong> (Semester {{ $certificate->student->current_semester }}) in the Department of
        <strong>{{ $certificate->student->department->name }}</strong>.
        @if($certificate->remarks)
            <br><br><em>Remarks / Purpose: {{ $certificate->remarks }}</em>
        @endif
    </p>

    <div class="flex items-end justify-between pt-6 border-t border-slate-200">
        <div class="text-xs text-slate-500">
            <div class="font-semibold text-slate-700 mb-1">QR Verification Token:</div>
            <code class="bg-slate-100 px-2 py-1 rounded text-[11px]">{{ $certificate->verification_token }}</code>
            <div class="mt-1"><a href="{{ route('certificates.verify', $certificate->verification_token) }}" target="_blank" class="text-indigo-600 underline">Verify Online ↗</a></div>
        </div>
        <div class="text-right text-xs font-semibold text-slate-800">
            <div>_________________________</div>
            <div class="mt-1">Principal / Registrar</div>
        </div>
    </div>
</div>
@endsection

