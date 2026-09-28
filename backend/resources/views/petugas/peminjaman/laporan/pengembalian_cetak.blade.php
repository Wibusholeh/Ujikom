<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pengembalian Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900 p-8" onload="window.print()">
    <div class="max-w-4xl mx-auto">
        <div class="text-center border-b pb-4 mb-6">
            <h1 class="text-2xl font-bold uppercase">Laporan Pengembalian Alat</h1>
            <p class="text-sm text-gray-600">Sistem Peminjaman Alat</p>
            @if($dariTanggal && $sampaiTanggal)
                <p class="text-xs text-gray-500 mt-1">Periode: {{ $dariTanggal }} s/d {{ $sampaiTanggal }}</p>
            @endif
        </div>

        <table class="w-full border-collapse border border-gray-300 text-sm mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left">Tgl Kembali</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Peminjam</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Petugas Verifikasi</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Kondisi</th>
                    <th class="border border-gray-300 px-4 py-2 text-right">Denda</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengembalian as $item)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $item->tgl_kembali }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $item->petugas->name ?? '-' }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $item->kondisi_alat ?? '-' }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border border-gray-300 px-4 py-4 text-center text-gray-500">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="text-right">
                <span class="font-bold text-gray-700 mr-4">Total Denda:</span>
                <span class="font-bold text-lg">Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</body>
</html>