@extends('layouts.app')

@section('content')
<div style="padding: 20px;">
    <h2 style="margin-bottom: 20px;">Pemantauan Pengembalian</h2>
    
    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h4 style="margin-bottom: 15px; font-size: 18px; font-weight: bold;">Daftar Alat Sedang Dipinjam</h4>
        
        <!-- Form Pencarian -->
        <form method="GET" action="{{ route('petugas.pemantauan') }}" style="margin-bottom: 20px; display: flex; gap: 10px; max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama peminjam..." value="{{ request('search') }}" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex: 1;">
            <button type="submit" style="padding: 8px 16px; background: #212529; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cari</button>
        </form>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; border: 1px solid #dee2e6;">PEMINJAM</th>
                        <th style="padding: 12px; border: 1px solid #dee2e6;">TANGGAL PINJAM / RENCANA KEMBALI</th>
                        <th style="padding: 12px; border: 1px solid #dee2e6;">DETAIL ALAT DIPINJAM</th>
                        <th style="padding: 12px; border: 1px solid #dee2e6;">STATUS WAKTU</th>
                        <th style="padding: 12px; border: 1px solid #dee2e6;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $peminjaman)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; border: 1px solid #dee2e6; vertical-align: top;">{{ $peminjaman->user->name ?? '-' }}</td>
                        <td style="padding: 12px; border: 1px solid #dee2e6; vertical-align: top;">
                            <span style="color: #6c757d; font-size: 12px;">Pinjam:</span> {{ $peminjaman->tgl_pinjam ?? '-' }}<br>
                            <span style="color: #6c757d; font-size: 12px;">Rencana:</span> {{ $peminjaman->tgl_kembali_plan ?? '-' }}
                        </td>
                        <td style="padding: 12px; border: 1px solid #dee2e6; vertical-align: top;">
                            <ul style="margin: 0; padding-left: 15px;">
                                @foreach($peminjaman->detailPinjams as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }} (Jumlah: {{ $detail->jumlah }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="padding: 12px; border: 1px solid #dee2e6; vertical-align: top;">
                            @if(now()->greaterThan($peminjaman->tgl_kembali_plan))
                                <span style="background: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Terlambat / Lewat Waktu</span>
                            @else
                                <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Tepat Waktu / Berjalan</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border: 1px solid #dee2e6; vertical-align: top;">
                            <a href="#" style="background: #198754; color: #fff; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px; display: inline-block;">Proses Pengembalian</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; border: 1px solid #dee2e6;">Tidak ada data peminjaman yang aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div style="margin-top: 20px;">
            {{ $peminjamans->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection