@extends('layouts.app')
@section('title', 'Marksheet & Transcript')
@section('header', 'Official Statement of Marks & SGPA/CGPA Transcript')

@section('content')
<div class="max-w-3xl mx-auto bg-white border-2 border-slate-800 rounded-xl p-8">
    <div class="flex justify-between items-start border-b-2 border-slate-800 pb-4 mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900 uppercase">{{ $student->college->name ?? 'WebotApp Institute of Technology' }}</h2>
            <p class="text-xs text-slate-600">{{ $student->college->university_affiliation ?? 'Autonomous / Affiliated University' }} &bull; NEP 2020 CBCS Grade Card</p>
        </div>
        <button onclick="window.print()" class="no-print px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-semibold">Print Transcript</button>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
        <div><strong>Student Name:</strong> {{ $student->full_name }}</div>
        <div><strong>Unique Student ID:</strong> {{ $student->unique_student_id }}</div>
        <div><strong>Registration No:</strong> {{ $student->registration_number }}</div>
        <div><strong>Programme:</strong> {{ $student->course->name }}</div>
        <div><strong>Department:</strong> {{ $student->department->name }}</div>
        <div><strong>Current Semester:</strong> {{ $student->current_semester }}</div>
    </div>

    <table class="w-full text-left text-xs border border-slate-300 mb-6">
        <thead class="bg-slate-100 border-b border-slate-300">
            <tr>
                <th class="p-2">Code</th>
                <th class="p-2">Subject Title</th>
                <th class="p-2">Credits</th>
                <th class="p-2">Internal</th>
                <th class="p-2">External</th>
                <th class="p-2">Total</th>
                <th class="p-2">Grade</th>
                <th class="p-2">GP</th>
                <th class="p-2">Result</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($marks as $m)
                <tr>
                    <td class="p-2 font-semibold">{{ $m->subject->code ?? '' }}</td>
                    <td class="p-2">{{ $m->subject->name ?? '' }}</td>
                    <td class="p-2">{{ $m->credits }}</td>
                    <td class="p-2">{{ $m->internal_marks }}</td>
                    <td class="p-2">{{ $m->external_marks }}</td>
                    <td class="p-2 font-bold">{{ $m->total_marks }}</td>
                    <td class="p-2 font-bold text-indigo-700">{{ $m->grade }}</td>
                    <td class="p-2">{{ $m->grade_point }}</td>
                    <td class="p-2">{{ $m->result_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex items-center justify-between bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-sm font-bold text-indigo-900 mb-6">
        <span>Total Credits Earned: {{ $totalCredits }}</span>
        <span>SGPA: {{ $sgpa }} / 10.00</span>
        <span>CGPA: {{ $sgpa }} / 10.00</span>
    </div>

    <div class="flex justify-between text-xs text-slate-600 pt-6 border-t border-slate-200">
        <span>Date: {{ now()->format('d M Y') }}</span>
        <span>Controller of Examinations &bull; Principal</span>
    </div>
</div>
@endsection

