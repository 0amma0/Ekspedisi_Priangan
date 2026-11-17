@extends('layout')
@section('isi')
    <form method="POST" action="{{ url('/paket') }}" class="bg-white p-6 rounded max-w-2xl space-y-3">
        @csrf
        <h1 class="text-xl font-bold">Input Paket Baru</h1>
        <select name="customer_id" class="border p-2 w-full">
            <option value="">-- Pilih pengirim --</option>
            @foreach ($pelanggans as $p)
                <option value="{{ $p->id }}" @selected(old('customer_id') == $p->id)>{{ $p->name }} ({{ $p->phone }})
                </option>
            @endforeach
        </select>
        <input name="recipient_name" value="{{ old('recipient_name') }}" placeholder="Nama penerima"
            class="border p-2 w-full">
        <input name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="Telepon penerima"
            class="border p-2 w-full">
        <textarea name="recipient_address" placeholder="Alamat penerima" class="border p-2 w-full">{{ old('recipient_address') }}</textarea>


        <input name="destination_city" id="destination_city" value="{{ old('destination_city') }}" placeholder="Kota tujuan"
            class="border p-2 w-full">

        <div id="zona-info" class="text-sm text-gray-600">
            Masukkan kota tujuan untuk mencari zona tarif.
        </div>

        <div id="zona-manual-wrapper" class="hidden">
            <label class="block font-medium mb-1">
                Pilih Zona Manual
            </label>

            <select name="zona_manual" id="zona_manual" class="border p-2 w-full" disabled>
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

        <input name="weight_kg" value="{{ old('weight_kg') }}" placeholder="Berat (kg), contoh 1.5"
            class="border p-2 w-full">
        <select name="service_type" class="border p-2 w-full">
            <option value="Reguler" @selected(old('service_type') === 'Reguler')>Reguler</option>
            <option value="Express" @selected(old('service_type') === 'Express')>Express (+50%)</option>
        </select>
        <label class="block"><input type="checkbox" name="is_cod" value="1" @checked(old('is_cod'))> Paket
            COD</label>
        <input name="cod_amount" value="{{ old('cod_amount') }}" placeholder="Nilai COD (rupiah)"
            class="border p-2 w-full">
        <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    zonaInfo.textContent = 'Masukkan kota tujuan.';
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

                        // Abaikan respons jika kota sudah berubah.
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
