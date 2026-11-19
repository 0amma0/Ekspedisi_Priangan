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
                    <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Pengirim
                    </label>

                    <select name="customer_id" id="customer_id"
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

                    <a href="{{ url('/pelanggan/create') }}"
                        class="inline-flex items-center gap-2 bg-amber-800 hover:bg-amber-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium mt-3 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Tambah Pelanggan
                    </a>
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
                    @foreach ([
            'recipient_name' => ['label' => 'Nama Penerima', 'placeholder' => 'Nama lengkap penerima'],
            'recipient_phone' => ['label' => 'Nomor Telepon Penerima', 'placeholder' => '081234567890'],
        ] as $field => $meta)
                        <div>
                            <label for="{{ $field }}" class="block text-sm font-medium text-slate-700 mb-2">
                                {{ $meta['label'] }}
                            </label>
                            <input type="text" name="{{ $field }}" id="{{ $field }}"
                                value="{{ old($field) }}" placeholder="{{ $meta['placeholder'] }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                            @error($field)
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <label for="recipient_address" class="block text-sm font-medium text-slate-700 mb-2">
                            Alamat Penerima
                        </label>
                        <textarea name="recipient_address" id="recipient_address" rows="3"
                            placeholder="Masukkan alamat lengkap penerima..."
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm resize-y focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">{{ old('recipient_address') }}</textarea>

                        @error('recipient_address')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- TUJUAN PENGIRIMAN --}}
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
                    <label for="destination_city" class="block text-sm font-medium text-slate-700 mb-2">
                        Kota Tujuan
                    </label>
                    <input type="text" name="destination_city" id="destination_city"
                        value="{{ old('destination_city') }}" placeholder="Contoh: Bandung"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                    @error('destination_city')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div id="zona-info"
                    class="flex items-start gap-3 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl p-4 mt-4 text-sm">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <span>Masukkan kota tujuan untuk mencari zona tarif.</span>
                </div>

                {{-- ZONA MANUAL (muncul jika kota tidak ditemukan) --}}
                <div id="zona-manual-wrapper" class="hidden mt-4">
                    <label for="zona_manual" class="block text-sm font-medium text-slate-700 mb-2">
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

                <input type="hidden" name="is_zone_manual" id="is_zone_manual" value="{{ old('is_zone_manual', '0') }}">
                <input type="hidden" name="zone_id" id="zone_id" value="{{ old('zone_id') }}">
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

                {{-- UKURAN --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-slate-700">Ukuran Paket</h3>
                        <span class="text-xs text-slate-400">Satuan cm</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ([
            'length_cm' => 'Panjang',
            'width_cm' => 'Lebar',
            'height_cm' => 'Tinggi',
        ] as $field => $label)
                            <div>
                                <label for="{{ $field }}" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ $label }}
                                </label>
                                <input type="number" step="0.01" min="0" name="{{ $field }}"
                                    id="{{ $field }}" value="{{ old($field) }}"
                                    placeholder="{{ $label }}"
                                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700">

                                @error($field)
                                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    {{-- TOMBOL INFO VOLUMETRIK --}}
                    <div class="mt-4">
                        <button type="button" id="btn-info-volumetrik"
                            class="inline-flex items-center gap-2 text-sm text-slate-700 hover:bg-purple-50 px-3 py-2 rounded-lg border border-purple-200 transition">
                            <i data-lucide="calculator" class="w-4 h-4"></i>
                            Baca selengkapnya tentang berat volumetrik
                        </button>
                    </div>

                    {{-- POPUP VOLUMETRIK --}}
                    <div id="popup-volumetrik"
                        class="hidden fixed z-9999] w-[320px] max-w-[90vw] bg-white border border-slate-200 text-slate-700 rounded-2xl shadow-2xl overflow-hidden"
                        style="top:120px;left:120px;">

                        <div id="drag-volumetrik"
                            class="bg-[#245C45] text-white px-4 py-3 flex items-center justify-between cursor-move select-none">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calculator" class="w-4 h-4"></i>
                                <span class="text-sm font-semibold">Info Berat Volumetrik</span>
                            </div>

                            <button type="button" id="close-volumetrik"
                                class="hover:bg-white/20 rounded-lg p-1 transition" title="Tutup informasi">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div class="p-4 space-y-3 text-sm text-slate-700">
                            <p class="font-semibold text-[#245C45]">Apa itu berat volumetrik?</p>

                            <p class="text-xs leading-relaxed">
                                Berat volumetrik merupakan perhitungan berat berdasarkan ukuran
                                atau volume paket. Paket yang berukuran besar tetapi ringan
                                dapat memiliki berat volumetrik lebih besar dari berat aktual.
                            </p>

                            <div class="bg-purple-50 border border-purple-100 rounded-xl p-3 space-y-2">
                                <p class="text-xs font-medium text-[#245C45]">Rumus perhitungan:</p>
                                <div class="text-center bg-white rounded-lg p-3 text-[#245C45] font-semibold">
                                    P × L × T ÷ 6000
                                </div>
                                <p class="text-xs text-[#245C45]">
                                    P = Panjang, L = Lebar, T = Tinggi (cm)
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-slate-800">Contoh perhitungan</p>
                                <div class="bg-slate-50 rounded-lg p-3 text-xs space-y-1">
                                    <p>30 × 20 × 20 ÷ 6000</p>
                                    <p class="font-semibold text-[#245C45]">= 2 kg</p>
                                </div>
                            </div>

                            <div class="bg-amber-100 border border-amber-200 rounded-xl p-3">
                                <p class="text-xs leading-relaxed text-amber-900">
                                    Sistem menggunakan berat terbesar antara berat aktual
                                    dan berat volumetrik sebagai dasar perhitungan ongkir.
                                    Jika ukuran tidak diisi lengkap, berat aktual digunakan.
                                </p>
                            </div>
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
                            <option value="Reguler" @selected(old('service_type', 'Reguler') === 'Reguler')>Reguler</option>
                            <option value="Express" @selected(old('service_type') === 'Express')>Express (+50%)</option>
                        </select>

                        @error('service_type')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ASURANSI --}}
            <section class="border-b border-slate-100 p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="is_insured" id="is_insured" value="1" @checked(old('is_insured'))
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-700 focus:ring-amber-600">

                    <div>
                        <label for="is_insured" class="font-semibold text-slate-800">
                            Gunakan Asuransi
                        </label>
                        <p class="mt-1 text-sm text-slate-500">
                            Lindungi barang dengan biaya asuransi sebesar 0,5% dari nilai barang.
                        </p>
                    </div>
                </div>

                <div id="insurance-value-wrapper" class="{{ old('is_insured') ? '' : 'hidden' }} mt-4">
                    <label for="declared_value" class="mb-2 block text-sm font-medium text-slate-700">
                        Nilai Barang (Rp)
                    </label>

                    <input type="number" name="declared_value" id="declared_value" min="1" step="1"
                        value="{{ old('declared_value') }}" placeholder="Contoh: 1000000"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-amber-600 focus:ring-2 focus:ring-amber-100">

                    @error('declared_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-500">
                        Biaya asuransi dihitung otomatis sebesar 0,5% dari nilai barang.
                    </p>
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
                        <p class="text-xs text-slate-500">
                            Penerima membayar barang saat paket diterima.
                        </p>
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

            {{-- TOMBOL AKSI --}}
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

    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const refreshIcons = () => window.lucide && lucide.createIcons();
            refreshIcons();

            // 1. TOGGLE INPUT NILAI ASURANSI
            const insuranceCheckbox = document.getElementById('is_insured');
            const insuranceWrapper = document.getElementById('insurance-value-wrapper');

            insuranceCheckbox?.addEventListener('change', () => {
                insuranceWrapper.classList.toggle('hidden', !insuranceCheckbox.checked);
            });

            // 2. POPUP INFO VOLUMETRIK (BUKA, TUTUP, & DRAG)
            const btnInfo = document.getElementById('btn-info-volumetrik');
            const popupInfo = document.getElementById('popup-volumetrik');
            const dragHeader = document.getElementById('drag-volumetrik');
            const closeInfo = document.getElementById('close-volumetrik');

            btnInfo?.addEventListener('click', () => {
                popupInfo.classList.toggle('hidden');
                refreshIcons();
            });

            closeInfo?.addEventListener('click', () => {
                popupInfo.classList.add('hidden');
            });

            let dragging = false;
            let offsetX = 0;
            let offsetY = 0;

            dragHeader?.addEventListener('pointerdown', (e) => {
                if (e.target.closest('button')) return;
                dragging = true;

                const rect = popupInfo.getBoundingClientRect();
                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;
                dragHeader.setPointerCapture(e.pointerId);
            });

            dragHeader?.addEventListener('pointermove', (e) => {
                if (!dragging) return;

                const maxX = window.innerWidth - popupInfo.offsetWidth;
                const maxY = window.innerHeight - popupInfo.offsetHeight;
                const x = Math.max(0, Math.min(e.clientX - offsetX, maxX));
                const y = Math.max(0, Math.min(e.clientY - offsetY, maxY));

                popupInfo.style.left = `${x}px`;
                popupInfo.style.top = `${y}px`;
            });

            ['pointerup', 'pointercancel'].forEach((eventName) => {
                dragHeader?.addEventListener(eventName, () => {
                    dragging = false;
                });
            });

            // 3. PENCARIAN ZONA OTOMATIS BERDASARKAN KOTA TUJUAN
            const kotaInput = document.getElementById('destination_city');
            const zonaInfo = document.getElementById('zona-info');
            const zonaManual = document.getElementById('zona-manual-wrapper');
            const zonaSelect = document.getElementById('zona_manual');
            const isManual = document.getElementById('is_zone_manual');
            const zoneId = document.getElementById('zone_id');

            let timer = null;
            let requestController = null;

            function renderZonaInfo(icon, message) {
                zonaInfo.innerHTML = `
            <i data-lucide="${icon}" class="w-4 h-4 shrink-0 mt-0.5"></i>
            <span></span>
        `;
                zonaInfo.querySelector('span').textContent = message;
                refreshIcons();
            }

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

            kotaInput?.addEventListener('input', () => {
                clearTimeout(timer);
                requestController?.abort();
                requestController = null;

                resetZona();

                const kota = kotaInput.value.trim();
                if (!kota) {
                    renderZonaInfo('info', 'Masukkan kota tujuan untuk mencari zona tarif.');
                    return;
                }

                renderZonaInfo('loader-circle', 'Sedang mencari zona tarif...');

                // Tunggu 500ms setelah user selesai mengetik (debounce) sebelum panggil API
                timer = setTimeout(async () => {
                    requestController = new AbortController();

                    try {
                        const url = `{{ route('zona.cek') }}?kota=${encodeURIComponent(kota)}`;
                        const response = await fetch(url, {
                            signal: requestController.signal
                        });

                        if (!response.ok) throw new Error('Permintaan gagal');

                        const data = await response.json();
                        if (kotaInput.value.trim() !== kota) return;

                        if (data.status === 'found') {
                            const tarifFormatted = Number(data.tariff).toLocaleString('id-ID');
                            renderZonaInfo('check-circle',
                                `${data.message}: ${data.zone_name}, tarif Rp${tarifFormatted}/kg`
                                );

                            zoneId.value = data.zone_id;
                            isManual.value = '0';
                            zonaSelect.disabled = true;
                            zonaManual.classList.add('hidden');
                        } else {
                            renderZonaInfo('alert-circle', data.message);
                            aktifkanManual();
                        }
                    } catch (error) {
                        if (error.name === 'AbortError' || kotaInput.value.trim() !== kota)
                            return;

                        renderZonaInfo('alert-triangle',
                            'Layanan pencarian zona tidak tersedia. Silakan pilih zona manual.'
                            );
                        aktifkanManual();
                    }
                }, 500);
            });

            // Jalankan ulang pencarian zona jika form kembali karena error validasi (old input)
            if (kotaInput?.value.trim()) {
                kotaInput.dispatchEvent(new Event('input'));
            }
        });
    </script>
@endsection
