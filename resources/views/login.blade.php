@extends('layouts.guest')
@section('title', 'Sign In')

@section('content')
<div class="max-w-md mx-auto mt-8 bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Sign in to WebotApp Indian College ERP</h2>
        <p class="text-xs text-slate-500 mt-1">Authentication is required to access the College Management System.</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@webotapp.edu.in" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                Keep me signed in
            </label>
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 text-white py-2.5 text-sm font-semibold hover:bg-indigo-700">Sign In</button>
    </form>
</div>
@endsection
