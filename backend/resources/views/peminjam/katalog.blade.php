<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Peminjam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
        @php
        $navUser = auth()->user();
        $navFoto = $navUser?->foto;
        $navPunyaFoto = $navFoto && file_exists(public_path($navFoto));
    @endphp
    <nav class="bg-blue-600 text-white px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <span class="font-semibold text-lg">Panel Peminjam</span>
            <span class="hidden sm:inline text-white/30">|</span>
            <div class="hidden sm:flex items-center gap-2">
                @if($navPunyaFoto)
                    <img src="{{ asset(implode('/', array_map('rawurlencode', explode('/', $navFoto)))) }}"
                         alt="Foto {{ $navUser->name }}"
                         class="w-7 h-7 rounded-full object-cover border border-white/40">
                @else
                    <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr($navUser->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <span class="text-sm text-white/80">Halo, {{ $navUser->name }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
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

    <div class="max-w-6xl mx-auto px-6 py-8 pb-28">

        @if(session('success'))
            <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Katalog Alat</h1>
                <p class="text-sm text-gray-500 mt-1">Pilih alat yang ingin kamu pinjam, lalu ajukan di bawah.</p>
            </div>
            <div class="relative w-full md:w-72">
                <input type="text" id="searchAlat" placeholder="Cari nama alat..."
                       class="w-full border border-gray-300 rounded-full pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            </div>
        </div>

        <form id="formPeminjaman" action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($alats as $alat)
                    <div class="alat-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col"
                         data-nama="{{ strtolower($alat->nama_alat) }}">

                        <div class="relative">
                            <label class="absolute top-3 right-3 z-10">
                                <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                                       class="chk-alat w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       data-target="jumlah-{{ $alat->id }}">
                            </label>

                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                                     class="w-full h-40 object-cover bg-gray-100">
                            @else
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $alat->nama_alat }}</h3>
                            <p class="text-xs text-gray-500 mt-1 flex-1">
                                {{ $alat->deskripsi ?? 'Tidak ada deskripsi untuk alat ini.' }}
                            </p>

                            <div class="flex items-center gap-1.5 text-xs text-gray-600 mt-3">
                                <span>{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                                <span class="text-gray-300">&middot;</span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full {{ strtolower($alat->status_kondisi) == 'baik' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $alat->status_kondisi }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <span class="text-sm text-gray-600">Stok {{ $alat->stok }}</span>

                                <div class="flex items-center border border-gray-300 rounded-full overflow-hidden">
                                    <button type="button" class="btn-kurang w-7 h-7 text-gray-600 hover:bg-gray-100" data-target="jumlah-{{ $alat->id }}">&minus;</button>
                                    <input type="number" id="jumlah-{{ $alat->id }}" name="jumlah[]"
                                           value="1" min="1" max="{{ $alat->stok }}" disabled
                                           class="w-10 text-center text-sm border-x border-gray-300 focus:outline-none disabled:bg-gray-50 disabled:text-gray-400">
                                    <button type="button" class="btn-tambah w-7 h-7 text-gray-600 hover:bg-gray-100" data-target="jumlah-{{ $alat->id }}">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-sm text-gray-500 py-10">
                        Tidak ada alat yang tersedia saat ini.
                    </div>
                @endforelse
            </div>

            <!-- Bar bawah: ringkasan & aksi -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-2px_8px_rgba(0,0,0,0.05)]">
                <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-4 text-sm">
                        <span class="font-semibold text-gray-800"><span id="jumlahDipilih">0</span> alat dipilih</span>
                        <div class="flex items-center gap-2">
                            <label for="tgl_kembali_plan" class="text-gray-600">Rencana Kembali</label>
                            <input type="datetime-local" id="tgl_kembali_plan" name="tgl_kembali_plan" required
                                   class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-full text-sm transition">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Aktifkan/nonaktifkan input jumlah sesuai status checkbox,
        // supaya alat yang tidak dicentang tidak ikut terkirim (jaga urutan array tetap sejajar).
        document.querySelectorAll('.chk-alat').forEach(chk => {
            chk.addEventListener('change', () => {
                const target = document.getElementById(chk.dataset.target);
                target.disabled = !chk.checked;
                updateCounter();
            });
        });

        document.querySelectorAll('.btn-kurang, .btn-tambah').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                let val = parseInt(input.value || '1', 10);
                const max = parseInt(input.max, 10);
                if (btn.classList.contains('btn-tambah')) {
                    val = Math.min(val + 1, max);
                } else {
                    val = Math.max(val - 1, 1);
                }
                input.value = val;
            });
        });

        function updateCounter() {
            const total = document.querySelectorAll('.chk-alat:checked').length;
            document.getElementById('jumlahDipilih').textContent = total;
        }

        document.getElementById('formPeminjaman').addEventListener('submit', function (e) {
            const total = document.querySelectorAll('.chk-alat:checked').length;
            if (total === 0) {
                e.preventDefault();
                alert('Pilih minimal satu alat sebelum mengajukan peminjaman.');
            }
        });

        // Pencarian sederhana di sisi browser
        document.getElementById('searchAlat').addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('.alat-card').forEach(card => {
                card.style.display = card.dataset.nama.includes(keyword) ? '' : 'none';
            });
        });
    </script>

</body>
</html>