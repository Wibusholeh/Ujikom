@extends('layouts.app')

@section('title', 'Laporan Pengembalian')

@section('header-title', 'Laporan Pengembalian Alat')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Filter Laporan</h2>
    </div>

    <form action="{{ route('admin.laporan.index') }}" method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Peminjam</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama peminjam..." class="border border-gray-300 rounded-md px-3 py-1.5 text-sm">
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-md text-sm transition">
            Tampilkan
        </button>
        <a href="{{ route('admin.laporan.cetak', request()->only('dari_tanggal', 'sampai_tanggal')) }}"
           target="_blank"
           class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-1.5 rounded-md text-sm transition">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Dikembalikan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjamans as $item)
                    @php $denda = optional($item->pengembalian)->denda ?? 0; @endphp
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional($item->user)->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->tgl_kembali_plan }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ optional($item->pengembalian)->tgl_kembali ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($denda > 0)
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">
                                    Rp {{ number_format($denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Tepat waktu</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection