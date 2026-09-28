@extends('layouts.app')

@section('title', 'Log Aktivitas Admin')
@section('header-title', 'Log Aktivitas Sistem')

@section('content')
<div class="container mx-auto px-4 py-2">
    
    <!-- Header & Search Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Log Aktivitas</h2>
            <p class="text-sm text-gray-600">Memantau seluruh aktivitas pengguna dan perubahan data di dalam sistem.</p>
        </div>
        
        <!-- Form Pencarian -->
        <form action="{{ route('admin.log-aktivitas.index') }}" method="GET" class="flex w-full md:w-auto gap-2">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}" 
                   placeholder="Cari aktivitas atau user..." 
                   class="px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                Cari
            </button>
            @if(isset($search) && $search != '')
                <a href="{{ route('admin.log-aktivitas.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm px-3 py-2 rounded-lg transition flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Log Aktivitas -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $index => $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $logs->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->user->name ?? 'User Tidak Dikenal' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if(optional($log->user)->role == 'admin')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Admin</span>
                                @elseif(optional($log->user)->role == 'petugas')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Petugas</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Peminjam</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $log->aktivitas }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection