@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Panel Petugas')

@section('header-title', 'Pemantauan Pengembalian')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Alat Sedang Dipinjam</h2>

        <form action="{{ route('petugas.pemantauan') }}" method="GET" class="flex w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..." class="border border-gray-300 rounded-l-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full md:w-64">
            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-1.5 rounded-r-md text-sm transition">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam / Rencana Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Alat Dipinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Waktu</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjamans as $item)
                    @php $telat = \Carbon\Carbon::parse($item->tgl_kembali_plan)->isPast(); @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ optional($item->user)->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div>Pinjam: <span class="text-gray-500">{{ $item->tgl_pinjam }}</span></div>
                            <div>Rencana: <span class="text-gray-500">{{ $item->tgl_kembali_plan }}</span></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <ul class="list-disc list-inside">
                                @foreach($item->detailPinjams as $detail)
                                    <li>{{ optional($detail->alat)->nama_alat ?? '-' }} <span class="text-xs text-gray-500">(Jumlah: {{ $detail->jumlah }})</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($telat)
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">Terlambat / Lewat Waktu</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Tepat Waktu</span>
                            @endif
                        </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center gap-2">
                                <button type="button"
                                        onclick="bukaModalSetujui('{{ route('petugas.pengembalian', $item->id) }}', '{{ optional($item->user)->name }}')"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Setujui
                                </button>

                                <form action="{{ route('petugas.pengembalian.tolak', $item->id) }}" method="POST"
                                      onsubmit="const c = prompt('Catatan untuk peminjam (boleh dikosongkan):'); if (c === null) return false; this.querySelector('[name=catatan]').value = c; return true;">
                                    @csrf
                                    <input type="hidden" name="catatan" value="">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada alat yang sedang dipinjam saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if(method_exists($peminjamans, 'links'))
            {{ $peminjamans->links() }}
        @endif
    </div>

        <!-- Modal Kondisi Barang -->
    <div id="modalSetujui" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Setujui Pengembalian</h3>
            <p class="text-sm text-gray-500 mb-4">Peminjam: <span id="namaPeminjamModal" class="font-medium text-gray-700"></span></p>

            <form id="formSetujui" method="POST">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Barang Saat Dikembalikan</label>
                <select name="kondisi_kembali" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="baik">Baik</option>
                    <option value="rusak_ringan">Rusak Ringan (denda Rp 20.000)</option>
                    <option value="rusak_sedang">Rusak Sedang (denda Rp 50.000)</option>
                    <option value="rusak_berat">Rusak Berat (denda Rp 100.000)</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="tutupModalSetujui()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-semibold transition">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalSetujui(url, namaPeminjam) {
            document.getElementById('formSetujui').action = url;
            document.getElementById('namaPeminjamModal').textContent = namaPeminjam;
            document.getElementById('modalSetujui').classList.remove('hidden');
            document.getElementById('modalSetujui').classList.add('flex');
        }
        function tutupModalSetujui() {
            document.getElementById('modalSetujui').classList.add('hidden');
            document.getElementById('modalSetujui').classList.remove('flex');
        }
    </script>
</div>
@endsection