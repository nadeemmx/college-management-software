@extends('layouts.app')
@section('title', 'Admit Card')
@section('header', 'Examination Admit Card / Hall Ticket')

@section('content')
<div class="max-w-2xl mx-auto bg-white border-2 border-slate-800 rounded-xl p-8">
    <div class="flex justify-between items-start border-b border-slate-300 pb-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900 uppercase">{{ $student->college->name ?? 'WebotApp Institute of Technology' }}</h2>
            <p class="text-xs font-semibold text-indigo-700">{{ $exam->name }} &mdash; HALL TICKET</p>
        </div>
        <button onclick="window.print()" class="no-print px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-semibold">Print Admit Card</button>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
        <div><strong>Student Name:</strong> {{ $student->full_name }}</div>
        <div><strong>Admit Card No:</strong> {{ $registration->admit_card_number }}</div>
        <div><strong>USN / ID:</strong> {{ $student->unique_student_id }}</div>
        <div><strong>Seat No:</strong> {{ $registration->seat_no }}</div>
        <div><strong>Programme:</strong> {{ $exam->course->name }}</div>
        <div><strong>Semester:</strong> {{ $exam->semester }}</div>
    </div>

    <table class="w-full text-left text-xs border border-slate-300 mb-6">
        <thead class="bg-slate-100 border-b border-slate-300">
            <tr><th class="p-2">Date</th><th class="p-2">Time</th><th class="p-2">Subject</th><th class="p-2">Hall / Room</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($exam->schedules as $sch)
                <tr>
                    <td class="p-2">{{ $sch->exam_date }}</td>
                    <td class="p-2">{{ substr($sch->start_time,0,5) }} - {{ substr($sch->end_time,0,5) }}</td>
                    <td class="p-2">{{ $sch->subject->code }} - {{ $sch->subject->name }}</td>
                    <td class="p-2">{{ $sch->room_no }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-between text-xs text-slate-600 pt-6 border-t border-slate-200">
        <span>Signature of Candidate</span>
        <span>Controller of Examinations</span>
    </div>
</div>
@endsection

