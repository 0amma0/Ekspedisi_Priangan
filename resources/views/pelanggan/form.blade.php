@extends('layout')
@section('isi')
    <form method="POST" class="bg-white p-6 rounded max-w-lg space-y-3"
        action="{{ $pelanggan->exists ? url('/pelanggan/' . $pelanggan->id) : url('/pelanggan') }}">
        @csrf
        @if ($pelanggan->exists)
            @method('PUT')
        @endif
        <h1 class="text-xl font-bold">{{ $pelanggan->exists ? 'Ubah' : 'Tambah' }} Pelanggan</h1>
        <input name="name" value="{{ old('name', $pelanggan->name) }}" placeholder="Nama" class="border p-2 w-full">
        <input name="phone" value="{{ old('phone', $pelanggan->phone) }}" placeholder="Telepon" class="border p-2 w-full">
        <textarea name="address" placeholder="Alamat" class="border p-2 w-full">{{ old('address', $pelanggan->address) }}</textarea>
        <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
    </form>
@endsection
