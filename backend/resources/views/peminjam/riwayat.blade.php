@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')
@section('header-title', 'Riwayat Peminjaman Alat')
@section('content')

<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <!-- Header Bagian Dalam dengan Tombol Kembali ke Katalog -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Riwayat Peminjaman Anda</h2>
        
        <!-- Tombol Kembali ke Katalog Peminjam -->
        <a href="{{ route('peminjam.katalog') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Dipinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Aktual Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjamans as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <ul class="list-disc list-inside">
                            @foreach($item->detailPinjams as $detail)
                                <li>{{ $detail->alat->nama_alat ?? '-' }} <span class="text-xs font-semibold text-gray-600">({{ $detail->jumlah }}x)</span></li>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($item->status == 'dikembalikan')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                        @elseif($item->status == 'telat')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Telat</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Dipinjam</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada riwayat peminjaman alat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection