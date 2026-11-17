<td class="px-4 py-3">
    <div class="flex flex-col items-start gap-2">

        @if ($p->status === 'Diterima')
            <form method="POST" action="{{ url('/paket/' . $p->id . '/transit') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    Ke Transit
                </button>
            </form>
        @elseif ($p->status === 'Transit' || ($p->status === 'Gagal Kirim' && $p->failed_attempts < 2))
            <form method="POST" action="{{ url('/paket/' . $p->id . '/tugaskan') }}" class="flex items-center gap-1.5">
                @csrf

                <select name="courier_id"
                    class="border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-amber-700">
                    @foreach ($kurirs as $k)
                        <option value="{{ $k->id }}">
                            {{ $k->code }} - {{ $k->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-amber-800 hover:bg-amber-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                    Tugaskan
                </button>
            </form>
        @elseif ($p->status === 'Gagal Kirim')
            <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
                onsubmit="return confirm('Catat setoran COD paket ini?')">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                    Catat Setoran
                </button>
            </form>
        @endif

        {{-- Setoran COD --}}
        @if ($p->is_cod && $p->status === 'Terkirim')
            @if ($p->cod_settled_at)
                <span
                    class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    Sudah Disetor
                </span>
            @else
                <form method="POST" action="{{ url('/paket/' . $p->id . '/setoran-cod') }}"
                    onsubmit="return confirm('Catat setoran COD paket ini?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-green-700 hover:bg-green-800 text-white px-3 py-2 rounded-lg text-xs font-semibold">
                        <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                        Catat Setoran
                    </button>
                </form>
            @endif
        @endif

    </div>
</td>
