@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')

@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')

    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        <p class="text-lg font-semibold">
            Selamat datang,
            <strong class="font-bold text-emerald-900">
                {{ auth()->user()->name }}
            </strong>
            Anda login sebagai hak akses
            <span class="uppercase font-bold text-emerald-900">
                {{ auth()->user()->role }}
            </span>.
        </p>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-2xl">🧰</div>
            <div class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_alat'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Alat</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-2xl">🗂️</div>
            <div class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_kategori'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Kategori</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-2xl">👥</div>
            <div class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_user'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total User</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-4">
            <div class="text-2xl">📦</div>
            <div class="text-2xl font-bold text-blue-700 mt-2">{{ $stats['peminjaman_aktif'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Peminjaman Aktif</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-4">
            <div class="text-2xl">⏳</div>
            <div class="text-2xl font-bold text-yellow-700 mt-2">{{ $stats['menunggu_persetujuan'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Menunggu Persetujuan</div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-red-200 p-4">
            <div class="text-2xl">💰</div>
            <div class="text-xl font-bold text-red-700 mt-2">Rp {{ number_format($stats['denda_bulan_ini'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Denda Bulan Ini</div>
        </div>
    </div>

    <!-- Grafik & Alat Terpopuler -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Pengajuan Peminjaman (7 Hari Terakhir)</h3>
            <canvas id="chartTren" height="110"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">🏆 Alat Terpopuler</h3>

            @forelse($alatTerpopuler as $index => $item)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs font-bold text-gray-400 w-5">#{{ $index + 1 }}</span>
                        <span class="text-sm text-gray-700 truncate">{{ optional($item->alat)->nama_alat ?? 'Alat dihapus' }}</span>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full whitespace-nowrap">
                        {{ $item->total }}x
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data peminjaman.</p>
            @endforelse
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('chartTren');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($tren->pluck('label')) !!},
                datasets: [{
                    label: 'Pengajuan Peminjaman',
                    data: {!! json_encode($tren->pluck('jumlah')) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    </script>

@endsection