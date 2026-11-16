<form action="{{ route('password.update') }}" method="POST">
    @csrf

    <h2 class="font-bold mb-3">Ganti Password</h2>

    <div class="mb-3">
        <label>Password Lama</label>
        <input type="password" name="password_lama"
            class="border p-2 w-full" required>
        @error('password_lama')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label>Password Baru</label>
        <input type="password" name="password_baru"
            class="border p-2 w-full" required>
        @error('password_baru')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="password_baru_confirmation"
            class="border p-2 w-full" required>
    </div>

    <button type="submit"
        class="bg-green-700 text-white px-4 py-2">
        Simpan Password
    </button>
</form>