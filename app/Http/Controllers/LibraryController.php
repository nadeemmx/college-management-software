<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $books = LibraryBook::when($request->filled('search'), function ($q) use ($request) {
            $s = $request->search;
            $q->where('title', 'like', "%{$s}%")->orWhere('author', 'like', "%{$s}%")->orWhere('isbn', 'like', "%{$s}%");
        })->latest()->get();

        $issues = LibraryIssue::with(['book', 'user'])->latest()->get();
        $members = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['student', 'faculty']))->get();

        return view('library.index', compact('books', 'issues', 'members'));
    }

    public function storeBook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'isbn' => 'nullable|string|max:30',
            'accession_no' => 'required|string|max:30|unique:library_books,accession_no',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'subject_category' => 'required|string|max:80',
            'rack_location' => 'nullable|string|max:40',
            'book_type' => 'required|in:Regular,Reference,Journal,Magazine,E-Book',
            'ebook_url' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1|max:500',
        ]);
        $data['available_copies'] = $data['total_copies'];
        $data['barcode'] = 'LIB' . rand(10000, 99999);
        LibraryBook::create($data);

        return back()->with('success', 'Book added to library catalogue.');
    }

    public function issueBook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'library_book_id' => 'required|exists:library_books,id',
            'user_id' => 'required|exists:users,id',
            'due_days' => 'required|integer|min:1|max:90',
        ]);

        $book = LibraryBook::findOrFail($data['library_book_id']);
        if ($book->available_copies < 1) {
            return back()->withErrors(['book' => 'No copies available for issue.']);
        }

        $user = User::with('role')->findOrFail($data['user_id']);
        $memberType = $user->role?->slug === 'faculty' ? 'Faculty' : 'Student';

        LibraryIssue::create([
            'library_book_id' => $book->id,
            'user_id' => $user->id,
            'member_type' => $memberType,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays((int) $data['due_days'])->toDateString(),
            'status' => 'Issued',
        ]);

        $book->decrement('available_copies');

        return back()->with('success', "Book '{$book->title}' issued to {$user->name}.");
    }

    public function returnBook(LibraryIssue $issue): RedirectResponse
    {
        if ($issue->status === 'Returned') {
            return back();
        }

        $today = now();
        $due = \Carbon\Carbon::parse($issue->due_date);
        $overdueDays = $today->gt($due) ? $due->diffInDays($today) : 0;
        $fine = $overdueDays * 5; // ₹5 per day overdue fine

        $issue->update([
            'return_date' => $today->toDateString(),
            'status' => 'Returned',
            'fine_amount' => $fine,
            'fine_paid' => $fine > 0,
        ]);

        $issue->book->increment('available_copies');

        return back()->with('success', "Book returned. Overdue fine calculated: ₹{$fine}.");
    }

    public function renewBook(LibraryIssue $issue): RedirectResponse
    {
        $newDue = \Carbon\Carbon::parse($issue->due_date)->addDays(14)->toDateString();
        $issue->update(['due_date' => $newDue, 'status' => 'Issued']);

        return back()->with('success', "Book renewed until {$newDue}.");
    }
}

