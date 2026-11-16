@extends('layout')
@section('isi')
    <h1 class="text-xl font-bold mb-3">
        Pelanggan</h1>
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
                <td>
                    @if (auth()->user()->role === 'admin')
                        <a class="text-blue-700" href="{{ url('/pelanggan/' . $p->id . '/edit') }}">Edit</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-3">Belum ada pelanggan.</td>
            </tr>
        @endforelse
    </table>
@endsection
