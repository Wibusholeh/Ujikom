<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPinjam extends Model
{
    protected $table = 'detail_pinjam';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && ($statusBaru == 'selesai' || $statusBaru == 'telat')) {
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }

                // Sesuai dengan fillable model Pengembalian Anda
                \App\Models\Pengembalian::firstOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'tgl_kembali' => now(),
                        'kondisi_kembali' => 'Baik', // Bisa disesuaikan jika ada input kondisi
                        'denda' => 0, // Bisa disesuaikan jika ada perhitungan denda
                        'petugas_id' => auth()->id(),
                    ]
                );
            }

            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Memperbarui status peminjaman #' . $peminjaman->id . ' menjadi ' . $statusBaru,
            ]);

            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}