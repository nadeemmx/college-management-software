@extends('layouts.app')
@section('title', $student->full_name)
@section('header', 'Student 360° Profile: ' . $student->full_name)

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $student->full_name }}</h2>
                <p class="text-xs text-slate-500">{{ $student->unique_student_id }} &bull; {{ $student->registration_number }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">{{ $student->status }}</span>
        </div>

        <div class="text-xs space-y-1.5 text-slate-700 border-t border-slate-100 pt-3">
            <div><strong>Programme:</strong> {{ $student->course->name }}</div>
            <div><strong>Department:</strong> {{ $student->department->name }}</div>
            <div><strong>Semester:</strong> {{ $student->current_semester }}</div>
            <div><strong>Category:</strong> {{ $student->category }} &bull; <strong>Blood Group:</strong> {{ $student->blood_group ?? 'N/A' }}</div>
            <div><strong>Email:</strong> {{ $student->email }}</div>
            <div><strong>Phone:</strong> {{ $student->phone }}</div>
            <div><strong>Address:</strong> {{ $student->permanent_address }}</div>
        </div>

        <form method="POST" action="{{ route('students.status', $student) }}" class="border-t border-slate-100 pt-3 space-y-2 text-xs">
            @csrf
            <div class="font-semibold text-slate-800">Update Status / Semester / Migration</div>
            <select name="status" class="w-full rounded border border-slate-300 px-2 py-1.5">
                @foreach(['Active','Passed Out','Suspended','Withdrawn'] as $st)<option @selected($student->status===$st)>{{ $st }}</option>@endforeach
            </select>
            <input type="number" name="current_semester" value="{{ $student->current_semester }}" class="w-full rounded border border-slate-300 px-2 py-1.5">
            <textarea name="transfer_migration_notes" placeholder="Transfer / Migration Notes" class="w-full rounded border border-slate-300 px-2 py-1.5">{{ $student->transfer_migration_notes }}</textarea>
            <button class="w-full rounded bg-indigo-600 text-white py-1.5 font-semibold">Save Changes</button>
        </form>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Guardians &amp; Documents</h3>
            <div class="grid md:grid-cols-2 gap-4 text-xs">
                <div>
                    <div class="font-semibold mb-2">Parent / Guardian Details</div>
                    @foreach($student->guardians as $g)
                        <div class="p-2 rounded bg-slate-50 border border-slate-100 mb-2">
                            <div class="font-medium">{{ $g->name }} ({{ $g->relation }})</div>
                            <div class="text-slate-500">{{ $g->phone }} &bull; {{ $g->occupation }}</div>
                        </div>
                    @endforeach
                </div>
                <div>
                    <div class="font-semibold mb-2">Upload Document</div>
                    <form method="POST" action="{{ route('students.documents', $student) }}" enctype="multipart/form-data" class="space-y-2">
                        @csrf
                        <input type="text" name="document_type" placeholder="Document Type (e.g. 12th Marksheet, TC)" required class="w-full rounded border border-slate-300 px-2 py-1.5">
                        <input type="file" name="document_file" required class="w-full">
                        <button class="px-3 py-1.5 rounded bg-slate-800 text-white">Upload &amp; Verify</button>
                    </form>
                    <div class="mt-2 space-y-1">
                        @foreach($student->documents as $doc)
                            <div class="text-[11px] text-emerald-700">✓ {{ $doc->document_type }} (Verified)</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-900">Academic Results &amp; Fee Accounts</h3>
                <div class="flex gap-2 text-xs">
                    <a href="{{ route('students.id_card', $student) }}" target="_blank" class="px-3 py-1 rounded bg-indigo-50 text-indigo-700 font-medium">Print ID Card</a>
                    <a href="{{ route('exams.marksheet', $student) }}" target="_blank" class="px-3 py-1 rounded bg-emerald-50 text-emerald-700 font-medium">Print Marksheet</a>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 text-xs">
                <div>
                    <div class="font-semibold mb-2">Fee Ledger</div>
                    @foreach($student->feeAccounts as $fa)
                        <div class="p-3 rounded bg-slate-50 border border-slate-200">
                            <div>Total: ₹{{ number_format($fa->total_amount, 2) }} &bull; Paid: ₹{{ number_format($fa->paid_amount, 2) }}</div>
                            <div class="font-bold text-rose-600 mt-1">Balance: ₹{{ number_format($fa->balance_amount, 2) }} ({{ $fa->status }})</div>
                        </div>
                    @endforeach
                </div>
                <div>
                    <div class="font-semibold mb-2">Issued Certificates</div>
                    @forelse($student->certificates as $cert)
                        <div class="p-2 rounded bg-slate-50 border border-slate-200 mb-1 flex justify-between">
                            <span>{{ $cert->certificate_type }} ({{ $cert->certificate_no }})</span>
                            <a href="{{ route('certificates.print', $cert) }}" target="_blank" class="text-indigo-600 underline">Print</a>
                        </div>
                    @empty
                        <div class="text-slate-400">No certificates issued yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

