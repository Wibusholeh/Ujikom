@extends('layouts.app') <!-- Sesuaikan dengan layout master Anda -->

@section('title', 'Cetak Laporan Pengembalian')
@section('header-title', 'Cetak Laporan Pengembalian')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('petugas.laporan.pengembalian') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border px-3 py-2">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('petugas.laporan.pengembalian') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg transition">
                Reset
            </a>
        </div>
        <div class="text-right">
            <a href="{{ route('petugas.laporan.pengembalian.cetak', ['dari_tanggal' => request('dari_tanggal'), 'sampai_tanggal' => request('sampai_tanggal')]) }}" target="_blank" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-medium px-4 py-2 rounded-lg transition shadow">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak / Print
            </a>
        </div>
    </form>
</div>

<!-- Tabel Laporan -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Laporan Pengembalian Alat</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Tgl Kembali</th>
                    <th class="px-6 py-3">Peminjam</th>
                    <th class="px-6 py-3">Petugas Verifikasi</th>
                    <th class="px-6 py-3">Kondisi</th>
                    <th class="px-6 py-3 text-right">Denda</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
                @forelse($pengembalian as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->tgl_kembali }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->petugas->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->kondisi_alat ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengembalian pada rentang tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Total Denda -->
    <div class="mt-6 flex justify-end border-t pt-4">
        <div class="text-right">
            <span class="text-base font-medium text-gray-600 mr-4">Total Denda:</span>
            <span class="text-lg font-bold text-gray-900">Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection