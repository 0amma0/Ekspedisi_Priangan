@extends('layout')

@section('isi')
    <div class="max-w-4xl mx-auto space-y-5">

        {{-- Header --}}
        <div>
            <a href="{{ url('/paket') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-amber-800 mb-3">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Paket
            </a>

            <h1 class="text-2xl font-bold text-slate-800">Input Paket Baru</h1>
            <p class="text-sm text-slate-500 mt-1">
                Masukkan informasi pengirim, penerima, dan detail pengiriman.
            </p>
        </div>

        <form method="POST" action="{{ url('/paket') }}"
            class="bg-white border border-slate-200 rounded-xl overflow-hidden space-y-0">
            @csrf

            {{-- Informasi Pengirim --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">
                        <i data-lucide="user-round" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Pengirim</h2>
                        <p class="text-xs text-slate-500">Pilih pelanggan yang mengirim paket.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nama Pengirim
                    </label>
                    <select name="customer_id"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        <option value="">-- Pilih pengirim --</option>
                        @foreach ($pelanggans as $p)
                            <option value="{{ $p->id }}" @selected(old('customer_id') == $p->id)>
                                {{ $p->name }} ({{ $p->phone }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Informasi Penerima --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Penerima</h2>
                        <p class="text-xs text-slate-500">Masukkan data tujuan pengiriman.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nama Penerima
                        </label>
                        <input name="recipient_name" value="{{ old('recipient_name') }}"
                            placeholder="Masukkan nama penerima"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        @error('recipient_name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nomor Telepon Penerima
                        </label>
                        <input name="recipient_phone" value="{{ old('recipient_phone') }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        @error('recipient_phone')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Alamat Penerima
                        </label>
                        <textarea name="recipient_address" rows="3" placeholder="Masukkan alamat lengkap penerima"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">{{ old('recipient_address') }}</textarea>
                        @error('recipient_address')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tujuan dan Zona --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-700 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Tujuan Pengiriman</h2>
                        <p class="text-xs text-slate-500">Zona tarif akan dicari secara otomatis.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Kota Tujuan
                    </label>
                    <input name="destination_city" id="destination_city" value="{{ old('destination_city') }}"
                        placeholder="Masukkan kota tujuan"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                    @error('destination_city')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="zona-info" class="flex items-start gap-2 bg-blue-50 text-blue-800 rounded-lg p-3 text-sm">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <span>Masukkan kota tujuan untuk mencari zona tarif.</span>
                </div>

                <div id="zona-manual-wrapper" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Pilih Zona Manual
                    </label>
                    <select name="zona_manual" id="zona_manual" disabled
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        <option value="">-- Pilih Zona --</option>
                        @foreach ($zonas as $z)
                            <option value="{{ $z->id }}" @selected(old('zona_manual') == $z->id)>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="is_zone_manual" id="is_zone_manual" value="0">
                <input type="hidden" name="zone_id" id="zone_id" value="">
            </div>

            {{-- Detail Paket --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                        <i data-lucide="weight" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Detail Paket</h2>
                        <p class="text-xs text-slate-500">Atur berat dan layanan pengiriman.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Berat Paket (kg)
                        </label>
                        <input name="weight_kg" value="{{ old('weight_kg') }}" placeholder="Contoh: 1.5"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        @error('weight_kg')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Jenis Layanan
                        </label>
                        <select name="service_type"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                            <option value="Reguler" @selected(old('service_type') === 'Reguler')>
                                Reguler
                            </option>
                            <option value="Express" @selected(old('service_type') === 'Express')>
                                Express (+50%)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- COD --}}
            <div class="p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Pembayaran COD</h2>
                        <p class="text-xs text-slate-500">Isi jika paket menggunakan layanan COD.</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_cod" value="1" @checked(old('is_cod'))
                        class="w-4 h-4 accent-amber-800 rounded">
                    Paket menggunakan COD
                </label>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nilai COD (Rupiah)
                    </label>
                    <input name="cod_amount" value="{{ old('cod_amount') }}" placeholder="Contoh: 150000"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                </div>
            </div>

            {{-- Tombol --}}
            <div
                class="bg-slate-50 border-t border-slate-100 p-5 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ url('/paket') }}"
                    class="text-center border border-slate-300 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Paket
                </button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            const kotaInput = document.getElementById('destination_city');
            const zonaInfo = document.getElementById('zona-info');
            const zonaManual = document.getElementById('zona-manual-wrapper');
            const zonaSelect = document.getElementById('zona_manual');
            const isManual = document.getElementById('is_zone_manual');
            const zoneId = document.getElementById('zone_id');

            let timer;

            function aktifkanManual() {
                zonaManual.classList.remove('hidden');
                zonaSelect.disabled = false;
                isManual.value = '1';
                zoneId.value = '';
            }

            function resetZona() {
                zonaManual.classList.add('hidden');
                zonaSelect.disabled = true;
                zonaSelect.value = '';
                isManual.value = '0';
                zoneId.value = '';
            }

            kotaInput.addEventListener('input', function() {
                clearTimeout(timer);
                resetZona();

                const kota = kotaInput.value.trim();

                if (!kota) {
                    zonaInfo.textContent = 'Masukkan kota tujuan untuk mencari zona tarif.';
                    return;
                }

                zonaInfo.textContent = 'Sedang mencari zona...';

                timer = setTimeout(async function() {
                    try {
                        const response = await fetch(
                            `{{ route('zona.cek') }}?kota=${encodeURIComponent(kota)}`
                        );

                        if (!response.ok) {
                            throw new Error('Permintaan gagal');
                        }

                        const data = await response.json();

                        if (kotaInput.value.trim() !== kota) return;

                        if (data.status === 'found') {
                            zonaInfo.textContent =
                                `${data.message}: ${data.zone_name}, tarif Rp${Number(data.tariff).toLocaleString('id-ID')}/kg`;

                            zoneId.value = data.zone_id;
                            isManual.value = '0';

                            zonaSelect.disabled = true;
                            zonaManual.classList.add('hidden');
                        } else {
                            zonaInfo.textContent = data.message;
                            aktifkanManual();
                        }

                    } catch (error) {
                        if (kotaInput.value.trim() !== kota) return;

                        zonaInfo.textContent =
                            'Layanan tidak tersedia. Silakan pilih zona manual.';

                        aktifkanManual();
                    }
                }, 500);
            });
        });
    </script>
@endsection