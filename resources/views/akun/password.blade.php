@extends('layout')

@section('isi')
    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-sm">

        <h1 class="text-xl font-bold mb-2">Pengaturan Akun</h1>
        <p class="text-gray-500 text-sm mb-6">
            Perbarui password akun kamu secara berkala.
        </p>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('akun.password.update') }}" method="POST">
            @csrf

            <h2 class="font-semibold mb-4">Ganti Password</h2>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium">
                    Password Lama
                </label>
                <input type="password" name="password_lama" class="border rounded w-full p-2" autocomplete="current-password"
                    required>

                @error('password_lama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium">
                    Password Baru
                </label>
                <input type="password" name="password_baru" class="border rounded w-full p-2" autocomplete="new-password"
                    required>

                @error('password_baru')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-1 text-sm font-medium">
                    Konfirmasi Password Baru
                </label>
                <input type="password" name="password_baru_confirmation" class="border rounded w-full p-2"
                    autocomplete="new-password" required>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ url()->previous() }}" class="border px-4 py-2 rounded">
                    Kembali
                </a>

                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
@endsection
