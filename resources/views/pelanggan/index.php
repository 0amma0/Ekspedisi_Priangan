@extends('layout')
@section('isi')
<h1 class="text-xl font-bold mb-3">Pelanggan</h1>
@if (auth()->user()->role === 'admin')
<a href="{{ url('/pelanggan/create') }}" class="bg-green-700 text-white px-3 py-2 inline-block mb-3">+ Pelanggan</a>
@endif
<table class="w-full bg-white text-sm">
    <tr class="bg-gray-200 text-left">
        <th class="p-2">Nama</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th></th>
    </tr>
    @forelse ($pelanggans as $p)
    <tr class="border-t">
        <td class="p-2">{{ $p->name }}</td>
        <td>{{ $p->phone }}</td>
        <td>{{ $p->address }}</td>
        <td>@if (auth()->user()->role === 'admin')<a class="text-blue-700" href="{{ url('/pelanggan/'.$p->id.'/edit') }}">Ubah</a>@endif</td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="p-3">Belum ada pelanggan.</td>
    </tr>
    @endforelse
</table>
@endsection

{{-- resources/views/pelanggan/form.blade.php --}}
@extends('layout')
@section('isi')
<form method="POST" class="bg-white p-6 rounded max-w-lg space-y-3"
    action="{{ $pelanggan->exists ? url('/pelanggan/'.$pelanggan->id) : url('/pelanggan') }}">
    @csrf
    @if ($pelanggan->exists) @method('PUT') @endif
    <h1 class="text-xl font-bold">{{ $pelanggan->exists ? 'Ubah' : 'Tambah' }} Pelanggan</h1>
    <input name="name" value="{{ old('name', $pelanggan->name) }}" placeholder="Nama" class="border p-2 w-full">
    <input name="phone" value="{{ old('phone', $pelanggan->phone) }}" placeholder="Telepon" class="border p-2 w-full">
    <textarea name="address" placeholder="Alamat" class="border p-2 w-full">{{ old('address', $pelanggan->address) }}</textarea>
    <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
</form>
@endsection