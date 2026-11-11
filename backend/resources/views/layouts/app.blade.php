<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="{{ auth()->check() && auth()->user()->role === 'peminjam' ? 'bg-gray-50 min-h-screen' : 'bg-gray-100 font-sans antialiased' }}">

    @if(auth()->check() && auth()->user()->role === 'peminjam')
        <!-- LAYOUT KHUSUS PEMINJAM (NAVBAR BIRU TANPA SIDEBAR) -->
        @php
            $navUser = auth()->user();
            $navFoto = $navUser?->foto;
            $navPunyaFoto = $navFoto && file_exists(public_path($navFoto));
        @endphp
        <nav class="bg-blue-600 text-white px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="font-semibold text-lg">Panel Peminjam</span>
                <span class="hidden sm:inline text-white/30">|</span>
                <a href="{{ route('profile.edit') }}" class="hidden sm:flex items-center gap-2 hover:bg-white/10 px-2.5 py-1 rounded-full transition" title="Profil Saya">
                    @if($navPunyaFoto)
                        <img src="{{ asset(implode('/', array_map('rawurlencode', explode('/', $navFoto)))) }}"
                             alt="Foto {{ $navUser->name }}"
                             class="w-7 h-7 rounded-full object-cover border border-white/40">
                    @else
                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr($navUser->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-sm text-white/90 hover:text-white">Halo, {{ $navUser->name }}</span>
                </a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('peminjam.katalog') }}" class="text-sm text-white/90 hover:text-white transition">
                    &larr; Kembali ke Katalog
                </a>
                <a href="{{ route('peminjam.riwayat') }}" class="text-sm text-white/90 hover:text-white transition">
                    Riwayat Pinjam
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-blue-600 text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-blue-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-6 py-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">@yield('header-title', 'Profil Saya')</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola informasi data diri, foto profil, dan kata sandi akun kamu.</p>
            </div>
            @yield('content')
        </main>

    @else
        <!-- LAYOUT KHUSUS ADMIN & PETUGAS (SIDEBAR HITAM) -->
        <div class="flex h-screen overflow-hidden">

            <!-- SIDEBAR -->
            <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        PANEL ADMIN
                    @elseif(auth()->check() && auth()->user()->role === 'petugas')
                        PANEL PETUGAS
                    @else
                        PANEL APLIKASI
                    @endif
                </div>
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    
                    <!-- MENU KHUSUS ADMIN -->
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" 
                           class="block px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.user.index') }}"
                           class="block px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.user.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola User
                        </a>

                        <a href="{{ route('admin.kategori.index') }}" 
                           class="block px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola Kategori
                        </a>

                        <a href="{{ route('admin.alat.index') }}" 
                           class="block px-4 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.alat.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola Alat
                        </a>

                        <a href="{{ route('admin.peminjaman.index') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.peminjaman*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola Peminjaman
                        </a>

                        <a href="{{ route('admin.pengembalian.index') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.pengembalian*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Kelola Pengembalian
                        </a>

                        <a href="{{ route('admin.log-aktivitas.index') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.log-aktivitas*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Log Aktivitas
                        </a>

                        <a href="{{ route('admin.laporan.index') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.laporan*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Cetak Laporan
                        </a>
                    @endif

                    <!-- MENU KHUSUS PETUGAS -->
                    @if(auth()->check() && auth()->user()->role === 'petugas')
                        <a href="{{ route('petugas.peminjaman.index') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.peminjaman*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Persetujuan Peminjaman
                        </a>

                        <a href="{{ route('petugas.pemantauan') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.pemantauan*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Pemantauan Pengembalian
                        </a>

                        <a href="{{ route('petugas.laporan.pengembalian') }}" 
                           class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.laporan*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Cetak Laporan
                        </a>
                    @endif

                    <!-- MENU PROFIL UNTUK ADMIN & PETUGAS -->
                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
                        <div class="pt-2 mt-2 border-t border-gray-800">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('profile.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                                Profil Saya
                            </a>
                        </div>
                    @endif

                </nav>

                @php
                    $sidebarUser = auth()->user();
                    $fotoPath = $sidebarUser?->foto;
                    $punyaFoto = $fotoPath && file_exists(public_path($fotoPath));
                @endphp
                <a href="{{ route('profile.edit') }}" class="p-4 border-t border-gray-800 text-sm text-gray-400 flex items-center gap-3 hover:bg-gray-800 transition">
                    @if($punyaFoto)
                        <img src="{{ asset(implode('/', array_map('rawurlencode', explode('/', $fotoPath)))) }}"
                             alt="Foto {{ $sidebarUser->name }}"
                             class="h-10 w-10 rounded-full object-cover border border-gray-700">
                    @else
                        <div class="h-10 w-10 rounded-full bg-gray-700 text-white flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($sidebarUser->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="overflow-hidden">
                        <div class="text-xs text-gray-400">Logged in as:</div>
                        <span class="text-white font-semibold truncate block">{{ $sidebarUser->name ?? 'User' }}</span>
                    </div>
                </a>
            </aside>

            <!-- MAIN CONTENT CONTAINER -->
            <div class="flex-1 flex flex-col overflow-y-auto">

                <!-- NAVBAR ATAS -->
                <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                    <div class="text-lg font-semibold text-gray-800">
                        @yield('header-title', 'Dashboard')
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <!-- KONTEN UTAMA HALAMAN -->
                <main class="flex-1 p-6">
                    @yield('content')
                </main>

            </div>
        </div>
    @endif

</body>
</html>