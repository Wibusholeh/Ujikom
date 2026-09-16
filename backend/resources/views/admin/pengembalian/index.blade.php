@extends('layouts.app')

@section('title', 'Kelola Pengembalian')

@section('header-title', 'Manajemen Pengembalian Alat')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Alat yang Sedang Dipinjam</h2>
        
        <!-- Form Pencarian -->
        <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex w-full md:w-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama peminjam..." class="border border-gray-300 rounded-l-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full md:w-64">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-r-md text-sm transition">Cari</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Dipinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjamans as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $peminjamans->firstItem() + $loop->index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <ul class="list-disc list-inside">
                            @foreach($item->detailPinjams as $detail)
                                <li>{{ $detail->alat->nama_alat ?? '-' }} <span class="text-xs font-semibold text-gray-600">({{ $detail->jumlah }}x)</span></li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tgl_pinjam }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tgl_kembali_plan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($item->status == 'telat')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Telat</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Dipinjam</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <form action="{{ route('admin.pengembalian.proses', $item->id) }}" method="POST" onsubmit="return confirm('Proses pengembalian alat ini? Stok akan otomatis dikembalikan.')" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs transition">Proses Kembali</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada alat yang sedang dipinjam saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection