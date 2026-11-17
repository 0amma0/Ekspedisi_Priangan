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
        <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
            onsubmit="return confirm('Catat setoran COD paket ini?')">
            @csrf
            <button class="text-red-700">
                Catat Setoran
            </button>
        </form>
    @endif

    {{-- FITUR SETORAN COD --}}
    @if ($p->is_cod && $p->status === 'Terkirim')
        @if ($p->cod_settled_at)
            <span class="block text-green-700 font-semibold mt-2">
                Sudah disetor
            </span>
        @else
            <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
                onsubmit="return confirm('Catat setoran?')">
                @csrf

                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-3 py-2 rounded">
                    Catat Setoran
                </button>
            </form>
        @endif
    @endif
</td>
