<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Mengubah kolom 'tgl_pinjam' yang tadinya 'date' menjadi 'datetime'
            // untuk mencatat tanggal dan jam saat peminjaman dibuat.
            $table->dateTime('tgl_pinjam')->change();

            // Mengubah kolom 'tgl_kembali_plan' yang tadinya 'date' menjadi 'datetime'
            // agar user bisa memilih tanggal dan jam rencana kembali.
            $table->dateTime('tgl_kembali_plan')->change();

            // Tambahkan kolom 'tgl_kembali_actual' (opsional, jika belum ada)
            // untuk mencatat jam aktual saat barang dikembalikan.
            $table->dateTime('tgl_kembali_actual')->nullable()->after('tgl_kembali_plan');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Rollback ke tipe data 'date' jika perlu
            $table->date('tgl_pinjam')->change();
            $table->date('tgl_kembali_plan')->change();
            $table->dropColumn('tgl_kembali_actual');
        });
    }
};