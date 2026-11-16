 @extends('layout')
 @section('isi')
     <form method="POST" action="{{ url('/kurir') }}" class="bg-white p-6 rounded max-w-lg space-y-3">
         @csrf
         <h1 class="text-xl font-bold">Tambah Kurir</h1>
         <input name="name" value="{{ old('name') }}" placeholder="Nama" class="border p-2 w-full">
         <input name="phone" value="{{ old('phone') }}" placeholder="Telepon" class="border p-2 w-full">
         <input name="password" placeholder="Password sementara (min 8)" class="border p-2 w-full">
         <button class="bg-amber-800 text-white px-4 py-2">Simpan</button>
     </form>
 @endsection
