@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Admin')
@section('header-title', 'Tambah Pengembalian Manual')

@section('content')

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-2xl bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Catat Pengembalian Alat</h3>
            <p class="text-sm text-gray-500 mt-1">
                Pilih peminjaman yang alatnya sudah dikembalikan. Stok alat akan bertambah dan denda dihitung otomatis.
            </p>
        </div>

        @if($peminjamans->isEmpty())
            <div class="p-5 text-sm text-gray-600">
                Tidak ada peminjaman yang sedang dipinjam saat ini, jadi belum ada yang bisa dicatat pengembaliannya.
            </div>
            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end">
                <a href="{{ route('admin.pengembalian.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Kembali
                </a>
            </div>
        @else
            <form action="{{ route('admin.pengembalian.store') }}" method="POST">
                @csrf

                <div class="p-5 space-y-5">

                    {{-- Peminjaman --}}
                    <div>
                        <label for="peminjaman_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Peminjaman
                        </label>
                        <select id="peminjaman_id" name="peminjaman_id" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Peminjaman --</option>
                            @foreach($peminjamans as $p)
                                <option value="{{ $p->id }}" {{ old('peminjaman_id') == $p->id ? 'selected' : '' }}>
                                    {{ optional($p->user)->name ?? '-' }}
                                    &mdash;
                                    {{ $p->detailPinjams->map(fn ($d) => (optional($d->alat)->nama_alat ?? '-') . ' (' . $d->jumlah . 'x)')->implode(', ') }}
                                    &mdash; rencana kembali {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                        @error('peminjaman_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal dikembalikan --}}
                    <div>
                        <label for="tgl_kembali" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Dikembalikan
                        </label>
                        <input type="date" id="tgl_kembali" name="tgl_kembali"
                               value="{{ old('tgl_kembali', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            Jika lewat dari rencana kembali, denda keterlambatan Rp 10.000 per hari otomatis ditambahkan.
                        </p>
                        @error('tgl_kembali')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <label for="kondisi_kembali" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kondisi Barang Saat Dikembalikan
                        </label>
                        <select id="kondisi_kembali" name="kondisi_kembali" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="baik" {{ old('kondisi_kembali', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ old('kondisi_kembali') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan (denda Rp 20.000)</option>
                            <option value="rusak_sedang" {{ old('kondisi_kembali') == 'rusak_sedang' ? 'selected' : '' }}>Rusak Sedang (denda Rp 50.000)</option>
                            <option value="rusak_berat" {{ old('kondisi_kembali') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat (denda Rp 100.000)</option>
                        </select>
                        @error('kondisi_kembali')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Petugas verifikasi (otomatis) --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-sm text-blue-800">
                        Diverifikasi oleh: <span class="font-semibold">{{ auth()->user()->name }}</span>
                        <span class="text-blue-600">(otomatis dari akun yang sedang login)</span>
                    </div>

                </div>

                <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <a href="{{ route('admin.pengembalian.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Simpan Pengembalian
                    </button>
                </div>
            </form>
        @endif
    </div>

@endsection