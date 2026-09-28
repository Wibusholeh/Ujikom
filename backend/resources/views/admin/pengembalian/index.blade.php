@extends('layouts.app')

@section('title', 'Kelola Pengembalian')

@section('header-title', 'Manajemen Pengembalian Alat')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Riwayat Pengembalian Alat</h2>
        
        <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex w-full md:w-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama peminjam..." class="border border-gray-300 rounded-l-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full md:w-64">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-r-md text-sm transition">Cari</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Alat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Dikembalikan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @if(isset($peminjamans) && count($peminjamans) > 0)
                    @php $no = $peminjamans->firstItem(); @endphp
                    @foreach($peminjamans as $item)
                    @php $denda = optional($item->pengembalian)->denda ?? 0; @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $no++ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ optional($item->user)->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $item->detailPinjams ? $item->detailPinjams->count() : 0 }} Jenis Alat Dipinjam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tgl_kembali_plan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional($item->pengembalian)->tgl_kembali ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->status == 'telat')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Telat</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($denda > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rp {{ number_format($denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pengembalian.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if(method_exists($peminjamans, 'links'))
            {{ $peminjamans->links() }}
        @endif
    </div>
</div>
@endsection