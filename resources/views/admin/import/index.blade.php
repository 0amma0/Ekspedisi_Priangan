@extends('layout')

@section('isi')
    <div class="container mx-auto px-6 py-6">

        <h1 class="text-2xl font-bold mb-2">
            Impor Data Awal
        </h1>

        <p class="text-gray-500 mb-6">
            Impor data kurir, pelanggan, dan paket historis.
        </p>

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-lg font-semibold mb-4">
                Upload File CSV
            </h2>

            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        File Kurir
                    </label>
                    <input type="file" name="kurir" accept=".csv" class="border rounded-lg p-2 w-full">
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        File Pelanggan
                    </label>
                    <input type="file" name="pelanggan" accept=".csv" class="border rounded-lg p-2 w-full">
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        File Paket Historis
                    </label>
                    <input type="file" name="paket" accept=".csv" class="border rounded-lg p-2 w-full">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg">
                    Mulai Impor
                </button>

            </form>

        </div>

    </div>
@endsection
