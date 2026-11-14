@extends('layout')
@section('isi')
    <h1 class="text-xl font-bold mb-3">Daftar Paket</h1>
    <form class="mb-3">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nomor resi" class="border p-2">
        <button class="bg-amber-800 text-white px-3 py-2">Cari</button>
    </form>
    <table class="w-full bg-white text-sm">
        <tr class="bg-gray-200 text-left">
            <th class="p-2">Resi</th>
            <th>Penerima</th>
            <th>Kota</th>
            <th>Status</th>
            <th>Gagal</th>
            <th>Kurir</th>
            <th>Aksi</th>
        </tr>
        @forelse ($pakets as $p)
            <tr class="border-t">
                <td class="p-2"><a class="text-blue-700" href="{{ url('/paket/' . $p->id) }}">{{ $p->tracking_number }}</a>
                </td>
                <td>{{ $p->recipient_name }}</td>
                <td>{{ $p->destination_city }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->failed_attempts }}</td>
                <td>{{ $p->courier?->name ?? '-' }}</td>
                <td>
                    @if ($p->status === 'Diterima')
                        <form method="POST" action="{{ url('/paket/' . $p->id . '/transit') }}">@csrf
                            <button class="text-blue-700">Ke Transit</button>
                        </form>
                    @elseif ($p->status === 'Transit' || ($p->status === 'Gagal Kirim' && $p->failed_attempts < 2))
                        <form method="POST" action="{{ url('/paket/' . $p->id . '/tugaskan') }}">@csrf
                            <select name="courier_id" class="border p-1">
                                @foreach ($kurirs as $k)
                                    <option value="{{ $k->id }}">{{ $k->code }} {{ $k->name }}</option>
                                @endforeach
                            </select>
                            <button class="text-blue-700">Tugaskan</button>
                        </form>
                    @elseif ($p->status === 'Gagal Kirim')
                        <form method="POST" action="{{ url('/paket/' . $p->id . '/retur') }}"
                            onsubmit="return confirm('Ubah ke Retur?')">@csrf
                            <button class="text-red-700">Retur</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-3">Belum ada paket.</td>
            </tr>
        @endforelse
    </table>
    <div class="mt-3">{{ $pakets->links() }}</div>
@endsection
