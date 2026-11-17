@extends('layouts.guest')
@section('title', 'Certificate QR Verification')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6 text-center">
    @if($certificate)
        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold mb-3">✓</div>
        <h2 class="text-lg font-bold text-slate-900">Authentic Digital Certificate</h2>
        <p class="text-xs text-slate-500 mb-4">Verified by WebotApp ERP Institutional Registry</p>
        <div class="text-left text-xs bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-1.5">
            <div><strong>Certificate No:</strong> {{ $certificate->certificate_no }}</div>
            <div><strong>Type:</strong> {{ $certificate->certificate_type }} Certificate</div>
            <div><strong>Student:</strong> {{ $certificate->student->full_name }} ({{ $certificate->student->unique_student_id }})</div>
            <div><strong>Programme:</strong> {{ $certificate->student->course->name }}</div>
            <div><strong>Issue Date:</strong> {{ $certificate->issue_date }}</div>
            <div><strong>Status:</strong> <span class="text-emerald-700 font-semibold">{{ $certificate->status }}</span></div>
        </div>
    @else
        <div class="w-12 h-12 mx-auto rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold mb-3">✕</div>
        <h2 class="text-lg font-bold text-rose-800">Invalid Verification Token</h2>
        <p class="text-xs text-slate-500 mt-1">No matching certificate found for token {{ $token }}.</p>
    @endif
</div>
@endsection

