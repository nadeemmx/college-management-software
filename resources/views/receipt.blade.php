@extends('layouts.app')
@section('title', 'Admission Receipt')
@section('header', 'Admission Receipt')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-slate-300 rounded-xl p-8">
    <div class="flex items-start justify-between border-b border-slate-200 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $application->course->department->college->name ?? 'WebotApp University Group' }}</h2>
            <p class="text-xs text-slate-500">Official Admission &amp; Application Fee Receipt</p>
        </div>
        <button onclick="window.print()" class="no-print px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-medium">Print Receipt</button>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm mb-6">
        <div><strong>Application No:</strong> {{ $application->application_no }}</div>
        <div><strong>Status:</strong> {{ $application->status }}</div>
        <div><strong>Applicant Name:</strong> {{ $application->full_name }}</div>
        <div><strong>Category:</strong> {{ $application->category }}</div>
        <div><strong>Programme:</strong> {{ $application->course->name }}</div>
        <div><strong>Registration No:</strong> {{ $application->registration_number ?? 'Pending' }}</div>
        <div><strong>Transaction ID:</strong> {{ $application->payment_txn_id }}</div>
        <div><strong>Amount Paid:</strong> ₹{{ number_format($application->application_fee_amount, 2) }}</div>
    </div>

    <div class="text-xs text-slate-500 border-t border-slate-200 pt-4 flex justify-between">
        <span>Generated on {{ now()->format('d M Y, h:i A') }}</span>
        <span>Authorized Signatory (Admissions)</span>
    </div>
</div>
@endsection

