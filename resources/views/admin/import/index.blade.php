@extends('layout')

@section('isi')
    <div class="container mx-auto px-6 py-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Impor Data
        </h1>

        <p class="text-gray-500 mb-6">
            Upload data kurir, pelanggan, dan paket melalui file CSV.
        </p>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        File Kurir
                    </label>
                    <input type="file" name="kurir" accept=".csv,.txt" class="w-full border rounded-lg p-2">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        File Pelanggan
                    </label>
                    <input type="file" name="pelanggan" accept=".csv,.txt" class="w-full border rounded-lg p-2">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        File Paket Historis
                    </label>
                    <input type="file" name="paket" accept=".csv,.txt" class="w-full border rounded-lg p-2">
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Mulai Impor
                </button>
            </form>
        </div>

        @if (session('import_report'))
            <div class="mt-6 rounded-lg border bg-white p-4">
                <h3 class="font-bold text-lg mb-3">Laporan Import</h3>

                @foreach (session('import_report') as $hasil)
                    <p class="mb-2">{{ $hasil }}</p>
                @endforeach
            </div>
        @endif

    </div>
@endsection
