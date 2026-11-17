@extends('layouts.app')
@section('title', 'Enquiry & Admission CRM')
@section('header', 'Enquiry & Admission CRM')

@section('content')
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Register Student Enquiry (Walk-in / Phone / Online)</h3>
        <form method="POST" action="{{ route('enquiries.store') }}" class="grid md:grid-cols-3 gap-3 text-xs">
            @csrf
            <input type="text" name="student_name" placeholder="Student Name *" required class="rounded-lg border border-slate-300 px-3 py-2">
            <input type="text" name="phone" placeholder="Mobile Number *" required class="rounded-lg border border-slate-300 px-3 py-2">
            <input type="email" name="email" placeholder="Email" class="rounded-lg border border-slate-300 px-3 py-2">
            <select name="source" class="rounded-lg border border-slate-300 px-3 py-2">
                <option>Walk-in</option><option>Telephone</option><option>Online</option><option>Campaign</option><option>Referral</option>
            </select>
            <select name="course_id" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Course Interest</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->code }}</option>
                @endforeach
            </select>
            <select name="counsellor_id" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Assign Counsellor</option>
                @foreach($counsellors as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <input type="text" name="campaign_name" placeholder="Campaign Name" class="rounded-lg border border-slate-300 px-3 py-2">
            <input type="date" name="next_followup_date" class="rounded-lg border border-slate-300 px-3 py-2">
            <button class="rounded-lg bg-indigo-600 text-white font-semibold py-2 hover:bg-indigo-700">Add Enquiry</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Bulk Communication</h3>
        <form method="POST" action="{{ route('enquiries.bulk_notify') }}" class="space-y-3 text-xs">
            @csrf
            <select name="channel" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option>WhatsApp</option><option>SMS</option><option>Email</option>
            </select>
            <textarea name="message" rows="2" placeholder="Message to all active leads..." required class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
            <button class="w-full rounded-lg bg-emerald-600 text-white font-semibold py-2 hover:bg-emerald-700">Send Bulk Campaign</button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
            <tr>
                <th class="px-4 py-3">Enquiry No</th>
                <th class="px-4 py-3">Lead</th>
                <th class="px-4 py-3">Source / Campaign</th>
                <th class="px-4 py-3">Course</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Log Follow-up &amp; Notes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($enquiries as $enq)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $enq->enquiry_no }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $enq->student_name }}</div>
                        <div class="text-slate-500">{{ $enq->phone }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $enq->source }} {{ $enq->campaign_name ? '('.$enq->campaign_name.')' : '' }}</td>
                    <td class="px-4 py-3">{{ $enq->course->code ?? '-' }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-slate-100 font-medium">{{ $enq->status }}</span></td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('enquiries.followup', $enq) }}" class="flex flex-wrap gap-1.5 items-center">
                            @csrf
                            <input type="text" name="notes" placeholder="Counselling note..." required class="rounded border border-slate-300 px-2 py-1 text-[11px] w-44">
                            <select name="status" class="rounded border border-slate-300 px-1.5 py-1 text-[11px]">
                                @foreach(['New','Follow-up','Converted','Lost'] as $st)
                                    <option @selected($enq->status === $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                            <button class="px-2.5 py-1 rounded bg-indigo-600 text-white text-[11px]">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

