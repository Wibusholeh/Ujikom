<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
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
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
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
            ->where('status', 'dipinjam') // Hanya tampilkan yang sedang dipinjam
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

        // Diubah agar mengarah ke folder peminjaman/laporan sesuai struktur Anda
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

        // Diubah agar mengarah ke folder peminjaman/laporan sesuai struktur Anda
        return view('petugas.peminjaman.laporan.pengembalian_cetak', compact('pengembalian', 'dariTanggal', 'sampaiTanggal', 'totalDenda'));
    }
}