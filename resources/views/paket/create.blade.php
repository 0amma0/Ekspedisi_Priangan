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
        <input name="destination_city" value="{{ old('destination_city') }}" placeholder="Kota tujuan"
            class="border p-2 w-full">
        <input name="weight_kg" value="{{ old('weight_kg') }}" placeholder="Berat (kg), contoh 1.5"
            class="border p-2 w-full">
        <select name="service_type" class="border p-2 w-full">
            <option value="Reguler" @selected(old('service_type') === 'Reguler')>Reguler</option>
            <option value="Express" @selected(old('service_type') === 'Express')>Express (+50%)</option>
        </select>
        <label class="block"><input type="checkbox" name="is_cod" value="1" @checked(old('is_cod'))> Paket
            COD</label>
        <input name="cod_amount" value="{{ old('cod_amount') }}" placeholder="Nilai COD (rupiah)" class="border p-2 w-full">
        <select name="zona_manual" class="border p-2 w-full">
            <option value="">Zona otomatis (Pusat Wilayah)</option>
            @foreach ($zonas as $z)
                <option value="{{ $z->id }}" @selected(old('zona_manual') == $z->id)>Manual: {{ $z->name }}</option>
            @endforeach
        </select>
        <p class="text-sm text-gray-600">Pilih zona manual hanya bila layanan Pusat Wilayah gagal.</p>
        <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
    </form>
@endsection
