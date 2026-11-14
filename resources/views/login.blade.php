@extends('layout')
@section('isi')
<form method="POST" action="{{ url('/login') }}" class="bg-white p-6 rounded max-w-sm mx-auto space-y-3">
    @csrf
    <h1 class="text-xl font-bold">Masuk Staf</h1>
    <input name="email" value="{{ old('email') }}" placeholder="Email" class="border p-2 w-full">
    <input name="password" type="password" placeholder="Password" class="border p-2 w-full">
    <button class="bg-amber-800 text-white px-4 py-2 w-full">Masuk</button>
</form>
@endsection