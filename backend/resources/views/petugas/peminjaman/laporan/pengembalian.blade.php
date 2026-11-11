@extends('layouts.app')

@section('title', 'Laporan Pengembalian - Petugas')

@section('header-title', 'Laporan Pengembalian Alat')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Filter Laporan</h2>
    </div>

    <form action="{{ route('petugas.laporan.pengembalian') }}" method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Nama Barang</label>
            <input type="text" name="nama_alat" value="{{ $namaAlat ?? request('nama_alat') }}" placeholder="Contoh: Tang, Proyektor..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm w-48 md:w-56 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-md text-sm font-medium transition">
            Tampilkan
        </button>
        @if($dariTanggal || $sampaiTanggal || ($namaAlat ?? request('nama_alat')))
            <a href="{{ route('petugas.laporan.pengembalian') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm transition">
                Reset
            </a>
        @endif
        <a href="{{ route('petugas.laporan.pengembalian.cetak', request()->only('dari_tanggal', 'sampai_tanggal', 'nama_alat')) }}"
           target="_blank"
           class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-1.5 rounded-md text-sm font-medium transition">
            Cetak Laporan
        </a>
    </form>

    <div class="mb-4 text-sm text-gray-600">
        Total denda pada data ini:
        <span class="font-semibold text-red-700">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail Alat Dipinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Dikembalikan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pengembalian as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional(optional($item->peminjaman)->user)->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if(optional($item->peminjaman)->detailPinjams && $item->peminjaman->detailPinjams->count())
                                <ul class="list-disc list-inside text-xs space-y-1">
                                    @foreach($item->peminjaman->detailPinjams as $detail)
                                        <li>
                                            <span class="font-medium">{{ optional($detail->alat)->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-gray-500">({{ $detail->jumlah }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ optional($item->peminjaman)->tgl_kembali_plan ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tgl_kembali ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($item->denda > 0)
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">
                                    Rp {{ number_format($item->denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Tidak Ada Denda</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection