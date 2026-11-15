@extends('layout')
@section('isi')
<form class="mb-4">
    <input name="resi" value="{{ $resi }}" placeholder="PKE-261116-0001" class="border p-2 w-72">
    <button class="bg-amber-800 text-white px-4 py-2">Cek Resi</button>
</form>
 
@if ($hasil === false)
    <div class="bg-red-100 p-3">Nomor resi tidak ditemukan.</div>
@elseif ($hasil)
    <div class="bg-white p-6 rounded max-w-xl">
        <h1 class="text-xl font-bold">{{ $hasil['resi'] }}</h1>
        <p>Status: <b>{{ $hasil['status'] }}</b></p>
        <p>Telepon penerima: {{ $hasil['telepon'] }}</p>
        <h2 class="font-bold mt-3">Riwayat</h2>
        @foreach ($hasil['riwayat'] as $h)
            <div class="border-t py-1">{{ $h['waktu'] }} &mdash; {{ $h['status'] }} {{ $h['catatan'] }}</div>
        @endforeach
    </div>
@endif
@endsection