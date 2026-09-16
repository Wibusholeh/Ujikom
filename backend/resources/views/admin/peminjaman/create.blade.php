@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Panel Admin')
@section('header-title', 'Form Tambah Transaksi Peminjaman')

@section('content')
<!-- Tambahkan CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Sedikit penyesuaian CSS agar tampilan Select2 cocok dengan Tailwind */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        padding-left: 12px !important;
    }
    /* Pastikan Select2 di dalam flexbox (daftar alat) menyesuaikan lebar */
    .alat-row .select2-container {
        flex: 1;
    }
</style>

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.peminjaman.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Peminjam (User)</label>
            <select name="user_id" required class="select2-user w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+3 days'))) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Bagian Daftar Alat yang Dipinjam (Dinamis) -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Daftar Alat yang Dipinjam</label>
            <div id="alat-container" class="space-y-3">
                <div class="flex items-center gap-2 alat-row">
                    <!-- Tambahkan class 'select2-alat' dan hapus class 'flex-1' karena sudah dihandle CSS di atas -->
                    <select name="alat_id[]" required class="select2-alat w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alats as $alat)
                            <option value="{{ $alat->id }}">{{ $alat->nama_alat }} (Stok: {{ $alat->stok }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="jumlah[]" value="1" min="1" placeholder="Jumlah" required
                        class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none h-[42px]">
                    <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600 transition h-[42px]">X</button>
                </div>
            </div>
            <button type="button" onclick="addRow()" class="mt-3 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                + Tambah Alat Lain
            </button>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.peminjaman.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan Peminjaman</button>
        </div>
    </form>
</div>

<!-- Tambahkan jQuery dan JS Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Buat konfigurasi Select2 Alat ke dalam variabel agar bisa dipakai berulang
    const select2AlatConfig = {
        width: '100%',
        placeholder: "-- Pilih Alat --",
        minimumInputLength: 1, // Wajib mengetik minimal 1 huruf
        language: {
            inputTooShort: function () {
                return "Ketik nama alat...";
            },
            noResults: function () {
                return "Alat tidak ditemukan.";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    };

    $(document).ready(function() {
        // Inisialisasi Select2 untuk Pilih Peminjam
        $('.select2-user').select2({
            width: '100%',
            placeholder: "-- Pilih User --",
            minimumInputLength: 1,
            language: {
                inputTooShort: function () { return "Ketik nama atau email user..."; },
                noResults: function () { return "Data user tidak ditemukan."; },
                searching: function() { return "Mencari..."; }
            }
        });

        // Inisialisasi Select2 untuk Daftar Alat (baris pertama)
        $('.select2-alat').select2(select2AlatConfig);
    });

    // Script jQuery untuk Tambah Baris Alat
    function addRow() {
        // Ambil baris pertama sebagai template
        let newRow = $('.alat-row:first').clone();
        
        // BERSILAN HASIL CLONE: Hapus elemen sisa Select2 bawaan dari hasil clone
        newRow.find('.select2-container').remove();
        newRow.find('select')
              .removeClass('select2-hidden-accessible') // Hapus class penanda select2
              .removeAttr('data-select2-id tabindex aria-hidden') // Hapus atribut sisa
              .val(''); // Reset nilai pilihan dropdown
        
        // Reset nilai input jumlah
        newRow.find('input[type="number"]').val('1');
        
        // Tambahkan baris baru ke dalam container
        $('#alat-container').append(newRow);
        
        // Inisialisasi ulang Select2 HANYA untuk elemen select di baris yang baru saja ditambahkan
        newRow.find('.select2-alat').select2(select2AlatConfig);
    }

    // Script jQuery untuk Hapus Baris Alat
    function removeRow(button) {
        if ($('.alat-row').length > 1) {
            $(button).closest('.alat-row').remove();
        } else {
            alert('Minimal harus ada 1 alat yang dipilih.');
        }
    }
</script>
@endsection