@extends('layout')
@section('isi')
<h1 class="text-xl font-bold mb-3">Kurir</h1>
@if (auth()->user()->role === 'admin')
<a href="{{ url('/kurir/create') }}" class="bg-green-700 text-white px-3 py-2 inline-block mb-3">+ Kurir</a>
@endif
<table class="w-full bg-white text-sm">
    <tr class="bg-gray-200 text-left">
        <th class="p-2">Kode</th>
        <th>Nama</th>
        <th>Telepon</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    @foreach ($kurirs as $k)
    <tr class="border-t">
        <td class="p-2">{{ $k->code }}</td>
        <td>{{ $k->name }}</td>
        <td>{{ $k->phone }}</td>
        <td>{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
            @if (auth()->user()->role === 'admin')
            <form method="POST" action="{{ url('/kurir/'.$k->id.($k->is_active ? '/nonaktif' : '/aktif')) }}" class="inline">@csrf
                <button class="text-blue-700">{{ $k->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
            <form method="POST" action="{{ url('/kurir/'.$k->id.'/reset') }}" class="inline">@csrf
                <input name="password" placeholder="password sementara" class="border p-1">
                <button class="text-blue-700">Reset</button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection

{{-- resources/views/kurir/form.blade.php --}}
@extends('layout')
@section('isi')
<form method="POST" action="{{ url('/kurir') }}" class="bg-white p-6 rounded max-w-lg space-y-3">
    @csrf
    <h1 class="text-xl font-bold">Tambah Kurir</h1>
    <input name="name" value="{{ old('name') }}" placeholder="Nama" class="border p-2 w-full">
    <input name="phone" value="{{ old('phone') }}" placeholder="Telepon" class="border p-2 w-full">
    <input name="password" placeholder="Password sementara (min 8)" class="border p-2 w-full">
    <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
</form>
@endsection