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
            @if (auth()->user()->role === 'admin')
                <th>Aksi</th>
            @endif
            <th> </th>
        </tr>
        @foreach ($kurirs as $k)
            <tr class="border-t">
                <td class="p-2">{{ $k->code }}</td>
                <td>{{ $k->name }}</td>
                <td>{{ $k->phone }}</td>
                <td>{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                <td>
                    @if (auth()->user()->role === 'admin')
                        <form method="POST" action="{{ url('/kurir/' . $k->id . ($k->is_active ? '/nonaktif' : '/aktif')) }}"
                            class="inline">@csrf
                            <button class="text-blue-700">{{ $k->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form method="POST" action="{{ url('/kurir/' . $k->id . '/reset') }}" class="inline">@csrf
                            <input name="password" placeholder="password sementara" class="border p-1">
                            <button class="text-blue-700">Reset</button>
                        </form>
                <td>
                    <a class="text-blue-700 text" class="text-right pr-4"
                        href="{{ url('/kurir/' . $k->id . '/edit') }} ">Edit</a>
                </td>
        @endif

        </td>
        </tr>
        @endforeach
    </table>
@endsection
