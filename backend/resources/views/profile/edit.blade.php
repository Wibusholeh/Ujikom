@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Saya')

@section('content')
@php
    $isSuperAdmin = (bool) ($user->is_super_admin || $user->email === 'admin@gmail.com' || $user->id === 1);
@endphp

<div class="{{ auth()->user()->role === 'peminjam' ? 'w-full' : 'max-w-4xl' }} space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-4 rounded-xl flex items-center gap-2 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Kartu Ringkasan Profil -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <!-- Banner Atas (Biru untuk Peminjam, Gelap untuk Admin/Petugas) -->
                <div class="{{ $user->role === 'peminjam' ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-gradient-to-r from-gray-900 to-slate-800' }} h-24"></div>

                <div class="px-6 pb-6 text-center">
                    @php
                        $punyaFoto = $user->foto && file_exists(public_path($user->foto));
                    @endphp

                    <!-- Foto Profil -->
                    <div class="relative w-24 h-24 mx-auto -mt-12 mb-3 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow flex items-center justify-center">
                        @if($punyaFoto)
                            <img id="previewFoto"
                                 src="{{ asset(implode('/', array_map('rawurlencode', explode('/', $user->foto)))) }}"
                                 alt="Foto {{ $user->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <img id="previewFoto" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            <span id="inisialFoto" class="text-3xl font-bold text-gray-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>

                    @if($isSuperAdmin)
                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 border border-amber-300 text-xs font-bold px-3 py-0.5 rounded-full mt-1">
                            👑 Super Admin
                        </span>
                    @elseif($user->role === 'admin')
                        <span class="inline-block bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-0.5 rounded-full capitalize mt-1">
                            Admin
                        </span>
                    @else
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-0.5 rounded-full capitalize mt-1">
                            {{ $user->role }}
                        </span>
                    @endif

                    <div class="border-t border-gray-100 mt-5 pt-4 text-left space-y-3 text-sm">
                        <div>
                            <div class="text-xs text-gray-400 uppercase font-medium">Email</div>
                            <div class="text-gray-700 font-medium truncate">{{ $user->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase font-medium">No. HP</div>
                            <div class="text-gray-700 font-medium">{{ $user->no_hp ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase font-medium">Bergabung Sejak</div>
                            <div class="text-gray-700 font-medium">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Edit Profil -->
        <div class="md:col-span-2">
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Edit Informasi Profil</h3>
                <p class="text-xs text-gray-500 mb-6">Perbarui data diri, foto profil, atau kata sandi akun Anda.</p>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email & No HP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text"
                                   name="no_hp"
                                   value="{{ old('no_hp', $user->no_hp) }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('no_hp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Upload Foto Profil -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Profil <span class="text-xs text-gray-400 font-normal">(JPG/PNG, Maks 2MB)</span></label>
                        <input type="file"
                               name="foto"
                               accept="image/jpeg,image/png,image/jpg"
                               onchange="tampilkanPreview(event)"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg p-1">
                        @error('foto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-gray-100 my-4">

                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Ubah Password <span class="text-xs font-normal text-gray-400">(Kosongkan jika tidak ingin mengganti password)</span></h4>
                    </div>

                    <!-- Password Baru & Konfirmasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password"
                                       name="password"
                                       id="passwordBaru"
                                       autocomplete="new-password"
                                       placeholder="Minimal 8 karakter"
                                       class="w-full border border-gray-300 rounded-lg pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="togglePass('passwordBaru')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password"
                                       name="password_confirmation"
                                       id="konfirmasiPasswordBaru"
                                       autocomplete="new-password"
                                       placeholder="Ulangi password baru"
                                       class="w-full border border-gray-300 rounded-lg pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="togglePass('konfirmasiPasswordBaru')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-full text-sm shadow transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePass(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function tampilkanPreview(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('previewFoto');
        const inisial = document.getElementById('inisialFoto');

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        if (inisial) {
            inisial.classList.add('hidden');
        }
    }
</script>
@endsection