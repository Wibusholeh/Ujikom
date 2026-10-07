<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
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
            <a href="{{ route('peminjam.katalog') }}" class="text-sm text-white/90 hover:text-white transition">
                &larr; Kembali ke Katalog
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white text-blue-600 text-sm font-semibold px-4 py-1.5 rounded-full hover:bg-blue-50 transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-8">

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

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Peminjaman</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar alat yang pernah dan sedang kamu pinjam.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Dipinjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($peminjamans as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($item->detailPinjams as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? '-' }} <span class="text-xs text-gray-500">({{ $detail->jumlah }}x)</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->tgl_kembali_plan ? \Carbon\Carbon::parse($item->tgl_kembali_plan)->translatedFormat('d F Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->tgl_kembali_actual ? \Carbon\Carbon::parse($item->tgl_kembali_actual)->translatedFormat('d F Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                {{ str_replace('_', ' ', $item->kondisi_kembali ?? '-') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col items-start gap-2">
                                    @if($item->status == 'dikembalikan')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Dikembalikan</span>
                                    @elseif($item->status == 'telat')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Telat</span>
                                    @elseif($item->status == 'pengajuan_pengembalian')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Menunggu Persetujuan Petugas</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Dipinjam</span>
                                    @endif

                                    @if($item->status == 'dipinjam')
                                        <form action="{{ route('peminjam.pengembalian.aju', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin mengajukan pengembalian alat ini?')">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-full transition">
                                                Ajukan Pengembalian
                                            </button>
                                        </form>
                                    @endif

                                    @if(!empty($item->catatan_petugas))
                                        <div class="text-xs text-red-600 max-w-xs">
                                            Catatan: {{ $item->catatan_petugas }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada riwayat peminjaman alat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>