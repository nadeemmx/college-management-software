@extends('layouts.guest')
@section('title', 'Track Application')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <h1 class="text-xl font-bold text-slate-900 mb-2">Application Tracking Portal</h1>
    <p class="text-xs text-slate-500 mb-4">Enter your Application Number (e.g. <code class="bg-slate-100 px-1 rounded">APP-2026-1001</code>) to check verification, merit rank, and admission status.</p>

    <form method="GET" action="{{ route('admission.track') }}" class="flex gap-2 mb-6">
        <input type="text" name="application_no" value="{{ request('application_no') }}" placeholder="Enter Application Number..." class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Track Status</button>
    </form>

    @if($application)
        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <div class="font-bold text-slate-900">{{ $application->full_name }} ({{ $application->application_no }})</div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">{{ $application->status }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                <div><strong>Programme:</strong> {{ $application->course->name }}</div>
                <div><strong>Category:</strong> {{ $application->category }}</div>
                <div><strong>Merit Score:</strong> {{ $application->merit_score }}%</div>
                <div><strong>Merit Rank:</strong> {{ $application->merit_rank ?? 'Pending' }}</div>
                <div><strong>Fee Status:</strong> {{ $application->application_fee_status }} ({{ $application->payment_txn_id }})</div>
                <div><strong>Registration No:</strong> {{ $application->registration_number ?? 'Pending Confirmation' }}</div>
            </div>
        </div>
    @elseif(request()->filled('application_no'))
        <div class="text-sm text-rose-600">No application found with number "{{ request('application_no') }}".</div>
    @endif
</div>
@endsection

