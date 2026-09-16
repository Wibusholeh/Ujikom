@extends('layouts.app')

@section('title', 'Edit User - Panel Admin')
@section('header-title', 'Edit Pengguna Sistem')

@section('content')

    {{-- Error Validasi --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">
                Edit User
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Ubah data pengguna yang dipilih.
            </p>
        </div>

        {{-- Form (Ditambah enctype="multipart/form-data") --}}
        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="p-5 space-y-5">

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Kosongkan jika tidak ingin mengubah password">

                    <p class="mt-1 text-xs text-gray-500">
                        Kosongkan jika password tidak ingin diubah.
                    </p>

                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No HP --}}
                <div>
                    <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                        No. HP
                    </label>

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        value="{{ old('no_hp', $user->no_hp) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                    @error('no_hp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                        Role
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>
                            Petugas
                        </option>

                        <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>
                            Peminjam
                        </option>

                    </select>

                    @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto Profil (Baru Ditambahkan) --}}
                <div>
                    <label for="foto" class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Profil
                    </label>

                    @if($user->foto)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ asset($user->foto) }}" alt="Foto Lama" class="w-14 h-14 rounded-full object-cover border border-gray-300 shadow-sm">
                            <span class="text-xs text-gray-500">Foto saat ini</span>
                        </div>
                    @endif

                    <input
                        type="file"
                        id="foto"
                        name="foto"
                        accept="image/png, image/jpeg, image/jpg"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer">

                    <p class="mt-1 text-xs text-gray-500">
                        Format: JPG, JPEG, PNG (Maks. 2MB). Kosongkan jika tidak ingin mengganti foto.
                    </p>

                    @error('foto')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">

                <a
                    href="{{ route('admin.user.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>

                <button
                    type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Update User
                </button>

            </div>

        </form>

    </div>

@endsection