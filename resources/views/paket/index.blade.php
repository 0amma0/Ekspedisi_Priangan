@extends('layout')

@section('isi')
    <h1 class="text-xl font-bold mb-3">Daftar Paket</h1>

    <form class="mb-3">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nomor resi" class="border p-2">
        <button class="bg-amber-800 text-white px-3 py-2">
            Cari
        </button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full bg-white text-sm">
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Resi</th>
                <th>Penerima</th>
                <th>Telepon</th>
                <th>Kota</th>
                <th>Berat (kg)</th>
                <th>Verifikasi</th>
                <th>Status</th>
                <th>Gagal</th>
                <th>Kurir</th>
                <th>Aksi</th>
            </tr>

            @forelse ($pakets as $p)
                <tr class="border-t">
                    <td class="p-2">
                        <a class="text-blue-700" href="{{ url('/paket/' . $p->id) }}">
                            {{ $p->tracking_number }}
                        </a>
                    </td>

                    <td>{{ $p->recipient_name }}</td>

                    <td>
                        {{ \App\Support\Telepon::rapikan($p->recipient_phone ?? '') ?: '-' }}
                    </td>

                    <td>{{ $p->destination_city }}</td>

                    <td>
                        {{ $p->weight_kg !== null ? $p->weight_kg : '-' }}
                    </td>

                    <td>
                        @if ($p->needs_verification || $p->weight_kg === null)
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                                Perlu Verifikasi
                            </span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                Lengkap
                            </span>
                        @endif
                    </td>

                    <td>{{ $p->status }}</td>

                    <td>{{ $p->failed_attempts }}</td>

                    <td>{{ $p->courier?->name ?? '-' }}</td>

                    <td>
                        @if ($p->status === 'Diterima')
                            <form method="POST" action="{{ url('/paket/' . $p->id . '/transit') }}">
                                @csrf
                                <button class="text-blue-700">
                                    Ke Transit
                                </button>
                            </form>
                        @elseif ($p->status === 'Transit' || ($p->status === 'Gagal Kirim' && $p->failed_attempts < 2))
                            <form method="POST" action="{{ url('/paket/' . $p->id . '/tugaskan') }}">
                                @csrf

                                <select name="courier_id" class="border p-1">
                                    @foreach ($kurirs as $k)
                                        <option value="{{ $k->id }}">
                                            {{ $k->code }} {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="text-blue-700">
                                    Tugaskan
                                </button>
                            </form>
                        @elseif ($p->status === 'Gagal Kirim')
                            <form method="POST" action="{{ url('/paket/' . $p->id . '/retur') }}"
                                onsubmit="return confirm('Ubah ke Retur?')">
                                @csrf
                                <button class="text-red-700">
                                    Retur
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="p-3">
                        Belum ada paket.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="mt-3">
        {{ $pakets->links() }}
    </div>
@endsection
