<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('peminjaman', 'kondisi_kembali')) {
                $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_sedang', 'rusak_berat'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('peminjaman', 'denda')) {
                $table->decimal('denda', 10, 2)->default(0)->after('kondisi_kembali');
            }
            if (!Schema::hasColumn('peminjaman', 'tgl_kembali_actual')) {
                $table->timestamp('tgl_kembali_actual')->nullable()->after('denda');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['kondisi_kembali', 'denda', 'tgl_kembali_actual']);
        });
    }
};