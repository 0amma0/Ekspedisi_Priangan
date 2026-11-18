@extends('layout')

@section('isi')
    <div class="max-w-5xl mx-auto space-y-6 pb-8">

        {{-- HEADER --}}
        <div>
            <a href="{{ url('/paket') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-amber-800 mb-4 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Paket
            </a>

            <h1 class="text-2xl font-bold text-slate-800">Input Paket Baru</h1>
            <p class="text-sm text-slate-500 mt-1">
                Lengkapi informasi pengirim, penerima, dan detail pengiriman paket.
            </p>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ url('/paket') }}"
            class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf

            {{-- PENGIRIM --}}
            <section class="p-5 sm:p-6 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <i data-lucide="user-round" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Pengirim</h2>
                        <p class="text-xs text-slate-500">Pilih pelanggan yang mengirim paket.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Pengirim
                    </label>

                    <select name="customer_id"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                        <option value="">-- Pilih pengirim --</option>

                        @foreach ($pelanggans as $p)
                            <option value="{{ $p->id }}" @selected(old('customer_id') == $p->id)>
                                {{ $p->name }} ({{ $p->phone }})
                            </option>
                        @endforeach
                    </select>

                    @error('customer_id')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

            </section>

            {{-- PENERIMA --}}
            <section class="p-5 sm:p-6 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Informasi Penerima</h2>
                        <p class="text-xs text-slate-500">Masukkan informasi penerima paket.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Penerima
                        </label>

                        <input type="text" name="recipient_name" value="{{ old('recipient_name') }}"
                            placeholder="Nama lengkap penerima"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                        @error('recipient_name')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nomor Telepon Penerima
                        </label>

                        <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}"
                            placeholder="081234567890"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                        @error('recipient_phone')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Alamat Penerima
                        </label>

                        <textarea name="recipient_address" rows="3" placeholder="Masukkan alamat lengkap penerima..."
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm resize-y focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">{{ old('recipient_address') }}</textarea>

                        @error('recipient_address')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- TUJUAN --}}
            <section class="p-5 sm:p-6 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Tujuan Pengiriman</h2>
                        <p class="text-xs text-slate-500">Masukkan kota tujuan untuk menentukan zona tarif.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Kota Tujuan
                    </label>

                    <input type="text" name="destination_city" id="destination_city"
                        value="{{ old('destination_city') }}" placeholder="Contoh: Bandung"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('destination_city')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- INFORMASI ZONA --}}
                <div id="zona-info"
                    class="flex items-start gap-3 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl p-4 mt-4 text-sm">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <span>Masukkan kota tujuan untuk mencari zona tarif.</span>
                </div>

                {{-- ZONA MANUAL --}}
                <div id="zona-manual-wrapper" class="hidden mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Pilih Zona Manual
                    </label>

                    <select name="zona_manual" id="zona_manual" disabled
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
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

            </section>

            {{-- DETAIL PAKET --}}
            <section class="p-5 sm:p-6 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                        <i data-lucide="weight" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Detail Paket</h2>
                        <p class="text-xs text-slate-500">Lengkapi berat, ukuran, dan layanan paket.</p>
                    </div>
                </div>

                {{-- DIMENSI --}}
                <div class="mb-6">

                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-slate-700">
                            Ukuran Paket
                        </h3>
                        <span class="text-xs text-slate-400">Satuan cm</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                        <div>
                            <label for="length_cm" class="block text-sm font-medium text-slate-700 mb-2">
                                Panjang
                            </label>

                            <input type="number" step="0.01" min="0" name="length_cm" id="length_cm"
                                value="{{ old('length_cm') }}" placeholder="Panjang"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                            @error('length_cm')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="width_cm" class="block text-sm font-medium text-slate-700 mb-2">
                                Lebar
                            </label>

                            <input type="number" step="0.01" min="0" name="width_cm" id="width_cm"
                                value="{{ old('width_cm') }}" placeholder="Lebar"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                            @error('width_cm')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="height_cm" class="block text-sm font-medium text-slate-700 mb-2">
                                Tinggi
                            </label>

                            <input type="number" step="0.01" min="0" name="height_cm" id="height_cm"
                                value="{{ old('height_cm') }}" placeholder="Tinggi"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                            @error('height_cm')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- TOMBOL BACA INFO VOLUMETRIK --}}
                    <div class="mt-4">
                        <button type="button" id="btn-info-volumetrik"
                            class="inline-flex items-center gap-2 text-sm text-slate-700 hover:bg-purple-50 px-3 py-2 rounded-lg border border-purple-200 transition">
                            <i data-lucide="calculator" class="w-4 h-4"></i>
                            <span>Baca selengkapnya tentang berat volumetrik</span>
                        </button>
                    </div>

                    {{-- FLOATING INFO --}}
                    <div id="popup-volumetrik"
                        class="hidden fixed z-9999 w-[320px] max-w-[90vw] bg-white border text-slate-700 rounded-2xl shadow-2xl overflow-hidden"
                        style="top: 120px; left: 120px;">

                        {{-- HEADER / DRAG HANDLE --}}
                        <div id="drag-volumetrik"
                            class="bg-[#245C45] text-white px-4 py-3 flex items-center justify-between cursor-move select-none">

                            <div class="flex items-center gap-2">
                                <i data-lucide="calculator" class="w-4 h-4"></i>
                                <span class="text-sm font-semibold">Info Berat Volumetrik</span>
                            </div>

                            <button type="button" id="close-volumetrik"
                                class="hover:bg-purple-600 rounded-lg p-1 transition" title="Tutup informasi">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        {{-- ISI POPUP --}}
                        <div class="p-4 space-y-3 text-sm text-slate-700">

                            <p class="text-sm font-semibold text-[#245C45]">
                                Apa itu berat volumetrik?
                            </p>

                            <p class="text-xs leading-relaxed">
                                Berat volumetrik merupakan perhitungan berat berdasarkan ukuran
                                atau volume paket. Paket yang berukuran besar tetapi ringan
                                dapat memiliki berat volumetrik lebih besar dari berat aktual.
                            </p>

                            <div class="bg-purple-50 border border-purple-100 rounded-xl p-3 space-y-2">

                                <p class="text-xs font-medium text-[#245C45]">
                                    Rumus perhitungan:
                                </p>

                                <div class="text-center bg-white rounded-lg p-3 text-[#245C45] font-semibold text-sm">
                                    P × L × T ÷ 6000
                                </div>

                                <p class="text-xs text-[#245C45]">
                                    P = Panjang, L = Lebar, T = Tinggi (cm)
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-slate-800">
                                    Contoh perhitungan
                                </p>

                                <div class="bg-slate-50 rounded-lg p-3 text-xs space-y-1">
                                    <p>30 × 20 × 20 ÷ 6000</p>
                                    <p class="font-semibold text-[#245C45]">
                                        = 2 kg
                                    </p>
                                </div>
                            </div>

                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                                <p class="text-xs leading-relaxed text-amber-900">
                                    Sistem menggunakan berat terbesar antara berat aktual
                                    dan berat volumetrik sebagai dasar perhitungan ongkir.
                                    Jika ukuran tidak diisi lengkap, berat aktual digunakan.
                                </p>
                            </div>

                        </div>
                    </div>


                    {{-- BERAT DAN LAYANAN --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label for="weight_kg" class="block text-sm font-medium text-slate-700 mb-2">
                                Berat Aktual (kg)
                            </label>

                            <input type="number" step="0.01" min="0.01" name="weight_kg" id="weight_kg"
                                value="{{ old('weight_kg') }}" placeholder="Contoh: 1.5"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                            @error('weight_kg')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_type" class="block text-sm font-medium text-slate-700 mb-2">
                                Jenis Layanan
                            </label>

                            <select name="service_type" id="service_type"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">
                                <option value="Reguler" @selected(old('service_type') === 'Reguler')>
                                    Reguler
                                </option>
                                <option value="Express" @selected(old('service_type') === 'Express')>
                                    Express (+50%)
                                </option>
                            </select>

                            @error('service_type')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
            </section>

            {{-- COD --}}
            <section class="p-5 sm:p-6 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Pembayaran COD</h2>
                        <p class="text-xs text-slate-500">Pengaturan pembayaran saat paket diterima.</p>
                    </div>
                </div>

                <label
                    class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <input type="checkbox" name="is_cod" value="1" @checked(old('is_cod'))
                        class="w-4 h-4 accent-amber-800">

                    <div>
                        <p class="text-sm font-medium text-slate-700">Aktifkan COD</p>
                        <p class="text-xs text-slate-500">Penerima membayar barang saat paket diterima.</p>
                    </div>
                </label>

                <div class="mt-4">
                    <label for="cod_amount" class="block text-sm font-medium text-slate-700 mb-2">
                        Nilai COD (Rp)
                    </label>

                    <input type="number" min="0" name="cod_amount" id="cod_amount"
                        value="{{ old('cod_amount') }}" placeholder="Contoh: 150000"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('cod_amount')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

            </section>

            {{-- TOMBOL --}}
            <div class="bg-slate-50 p-5 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a href="{{ url('/paket') }}"
                    class="inline-flex items-center justify-center border border-slate-300 hover:bg-slate-100 text-slate-700 px-5 py-3 rounded-xl text-sm font-medium transition">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-5 py-3 rounded-xl text-sm font-medium transition shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Paket
                </button>

            </div>

        </form>
    </div>

    {{-- ICON --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- ZONA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // POPUP INFO VOLUMETRIK
            lucide.createIcons();
            const btnInfo = document.getElementById('btn-info-volumetrik');
            const popupInfo = document.getElementById('popup-volumetrik');
            const dragHeader = document.getElementById('drag-volumetrik');
            const closeInfo = document.getElementById('close-volumetrik');

            btnInfo.addEventListener('click', function() {
                popupInfo.classList.toggle('hidden');
                lucide.createIcons();
            });

            closeInfo.addEventListener('click', function() {
                popupInfo.classList.add('hidden');
            });

            let dragging = false;
            let offsetX = 0;
            let offsetY = 0;

            dragHeader.addEventListener('pointerdown', function(e) {

                if (e.target.closest('button')) return;

                dragging = true;

                const rect = popupInfo.getBoundingClientRect();

                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;

                dragHeader.setPointerCapture(e.pointerId);
            });

            dragHeader.addEventListener('pointermove', function(e) {

                if (!dragging) return;

                let x = e.clientX - offsetX;
                let y = e.clientY - offsetY;

                x = Math.max(0, Math.min(x, window.innerWidth - popupInfo.offsetWidth));
                y = Math.max(0, Math.min(y, window.innerHeight - popupInfo.offsetHeight));

                popupInfo.style.left = x + 'px';
                popupInfo.style.top = y + 'px';
            });

            dragHeader.addEventListener('pointerup', function() {
                dragging = false;
            });

            dragHeader.addEventListener('pointercancel', function() {
                dragging = false;
            });

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
                    zonaInfo.innerHTML = `
                        <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>Masukkan kota tujuan untuk mencari zona tarif.</span>
                    `;
                    lucide.createIcons();
                    return;
                }

                zonaInfo.innerHTML = `
                    <i data-lucide="loader-circle" class="w-4 h-4 shrink-0 mt-0.5 animate-spin"></i>
                    <span>Sedang mencari zona tarif...</span>
                `;
                lucide.createIcons();

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

                            zonaInfo.innerHTML = `
                                <i data-lucide="check-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                                <span>${data.message}: ${data.zone_name}, tarif Rp${Number(data.tariff).toLocaleString('id-ID')}/kg</span>
                            `;

                            zoneId.value = data.zone_id;
                            isManual.value = '0';

                            zonaSelect.disabled = true;
                            zonaManual.classList.add('hidden');

                        } else {

                            zonaInfo.innerHTML = `
                                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                                <span>${data.message}</span>
                            `;

                            aktifkanManual();
                        }

                        lucide.createIcons();

                    } catch (error) {

                        if (kotaInput.value.trim() !== kota) return;

                        zonaInfo.innerHTML = `
                            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                            <span>Layanan pencarian zona tidak tersedia. Silakan pilih zona manual.</span>
                        `;

                        aktifkanManual();
                        lucide.createIcons();
                    }

                }, 500);
            });

        });
    </script>
@endsection
