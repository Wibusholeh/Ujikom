<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Alias method 'index' yang memanggil logika 'indexPeminjaman' agar route sesuai
    public function index(Request $request)
    {
        return $this->indexPeminjaman($request);
    }

    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

            $alatDicoret = [];

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::find($detail->alat_id);

                if (!$alat || $alat->stok < $detail->jumlah) {
                    // Stok tidak cukup: coret alat ini saja dari pengajuan
                    $alatDicoret[] = $alat->nama_alat ?? 'Alat tidak ditemukan';
                    $detail->delete();
                    continue;
                }

                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            // Cek apakah masih ada alat yang berhasil diproses
            $sisaDetail = $peminjaman->detailPinjams()->count();

            if ($sisaDetail === 0) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Semua alat pada pengajuan ini sudah habis stoknya. Pengajuan tidak bisa disetujui.');
            }

            $peminjaman->update(['status' => 'dipinjam']);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menyetujui peminjaman #' . $peminjaman->id . ' (' . optional($peminjaman->user)->name . ')'
                    . (count($alatDicoret) ? ', alat dicoret karena stok habis: ' . implode(', ', $alatDicoret) : ''),
            ]);

            DB::commit();

            $pesan = 'Peminjaman disetujui dan stok alat dikurangi.';
            if (count($alatDicoret) > 0) {
                $pesan .= ' Catatan: alat berikut dicoret karena stok habis: ' . implode(', ', $alatDicoret) . '.';
            }

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak Peminjaman (Menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function pemantauanPengembalian(Request $request)
    {
        $keyword = $request->input('search');

                $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'pengajuan_pengembalian')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('tgl_kembali_plan', 'asc')
            ->paginate(10);

        return view('petugas.peminjaman.pemantauan', compact('peminjamans'));
    }

    public function pengembalianIndex()
    {
        return view('petugas.pengembalian.index');
    }

    /**
     * Halaman Laporan Pengembalian untuk Petugas
     */
    public function laporanPengembalian(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Pengembalian::with(['peminjaman.user', 'petugas']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('tgl_kembali', [$dariTanggal, $sampaiTanggal]);
        }

        $pengembalian = $query->get();
        $totalDenda = $pengembalian->sum('denda');

        return view('petugas.peminjaman.laporan.pengembalian', compact('pengembalian', 'dariTanggal', 'sampaiTanggal', 'totalDenda'));
    }

    /**
     * Halaman Cetak Laporan Pengembalian
     */
    public function cetakLaporanPengembalian(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Pengembalian::with(['peminjaman.user', 'petugas']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('tgl_kembali', [$dariTanggal, $sampaiTanggal]);
        }

        $pengembalian = $query->get();
        $totalDenda = $pengembalian->sum('denda');

        return view('petugas.peminjaman.laporan.pengembalian_cetak', compact('pengembalian', 'dariTanggal', 'sampaiTanggal', 'totalDenda'));
    }

        public function prosesPengembalian(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_sedang,rusak_berat',
        ]);

        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $sekarang = now();

        // Denda keterlambatan
        $dendaTelat = 0;
        $statusBaru = 'selesai';
        if ($sekarang->gt($tglRencana)) {
            $hariTelat = (int) ceil($tglRencana->diffInDays($sekarang));
            if ($hariTelat < 1) $hariTelat = 1;
            $dendaTelat = $hariTelat * 10000;
            $statusBaru = 'telat';
        }

        // Denda kerusakan
        $dendaKerusakan = match ($request->kondisi_kembali) {
            'rusak_ringan' => 20000,
            'rusak_sedang' => 50000,
            'rusak_berat'  => 100000,
            default        => 0,
        };

        $totalDenda = $dendaTelat + $dendaKerusakan;

        DB::beginTransaction();
        try {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => $statusBaru]);

            Pengembalian::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'tgl_kembali'      => $sekarang->toDateString(),
                    'kondisi_kembali'  => $request->kondisi_kembali,
                    'denda'            => $totalDenda,
                    'petugas_id'       => auth()->id(),
                ]
            );

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menyetujui pengembalian peminjaman #' . $peminjaman->id
                    . ' (' . optional($peminjaman->user)->name . '), kondisi: ' . $request->kondisi_kembali
                    . ($totalDenda > 0 ? ', denda: Rp ' . number_format($totalDenda, 0, ',', '.') : ''),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian disetujui. Total denda: Rp ' . number_format($totalDenda, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

        public function tolakPengembalian(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $catatan = $request->input('catatan') ?: 'Pengembalian tidak disetujui oleh petugas.';

        $peminjaman->status = 'dipinjam';
        $peminjaman->catatan_petugas = $catatan;
        $peminjaman->save();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menolak pengembalian peminjaman #' . $peminjaman->id . ': ' . $catatan,
        ]);

        return redirect()->back()->with('success', 'Pengembalian ditolak, peminjam akan melihat catatan ini.');
    }
}