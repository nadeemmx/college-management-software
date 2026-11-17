@extends('layouts.app')
@section('title', 'Online Admission Management')
@section('header', 'Online Admission Management & Merit List')

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="flex flex-wrap items-center gap-2 text-sm">
        <select name="course_id" class="rounded-lg border border-slate-300 px-3 py-1.5">
            <option value="">All Programmes</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->code }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-1.5">
            <option value="">All Statuses</option>
            @foreach(['Submitted','Verified','Merit Listed','Waiting List','Approved','Rejected','Confirmed','Cancelled'] as $st)
                <option @selected(request('status') == $st)>{{ $st }}</option>
            @endforeach
        </select>
        <button class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-medium">Filter</button>
    </form>

    <form method="POST" action="{{ route('admissions.merit') }}" class="flex items-center gap-2">
        @csrf
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">Generate Merit &amp; Waiting List</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
            <tr>
                <th class="px-4 py-3">App No</th>
                <th class="px-4 py-3">Applicant</th>
                <th class="px-4 py-3">Programme</th>
                <th class="px-4 py-3">12th % / Merit</th>
                <th class="px-4 py-3">Rank</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($applications as $app)
                <tr>
                    <td class="px-4 py-3 font-semibold text-indigo-700">{{ $app->application_no }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $app->full_name }}</div>
                        <div class="text-slate-500">{{ $app->category }} &bull; {{ $app->phone }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $app->course->code ?? '' }}</td>
                    <td class="px-4 py-3">{{ $app->class_12_percentage }}% / <strong>{{ $app->merit_score }}</strong></td>
                    <td class="px-4 py-3">{{ $app->merit_rank ? '#'.$app->merit_rank : '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full font-medium bg-slate-100 text-slate-800">{{ $app->status }}</span>
                        @if($app->registration_number)
                            <div class="text-[10px] text-emerald-700 mt-0.5">{{ $app->registration_number }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <form method="POST" action="{{ route('admissions.status', $app) }}" class="flex items-center gap-1">
                                @csrf
                                <select name="status" class="rounded border border-slate-300 px-1.5 py-1 text-[11px]">
                                    @foreach(['Verified','Merit Listed','Waiting List','Approved','Confirmed','Rejected','Cancelled'] as $st)
                                        <option value="{{ $st }}" @selected($app->status === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="px-2 py-1 rounded bg-indigo-600 text-white text-[11px]">Update</button>
                            </form>
                            <a href="{{ route('admissions.receipt', $app) }}" target="_blank" class="px-2 py-1 rounded border border-slate-300 text-[11px] hover:bg-slate-50">Receipt</a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

