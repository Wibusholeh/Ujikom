<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pengembalian</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #374151;
            margin: 0;
            padding: 32px 40px;
            background: #fff;
        }
        .no-print {
            margin-bottom: 20px;
        }
        .no-print button {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .no-print button:hover { background: #4338ca; }

        header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        header h1 {
            font-size: 19px;
            margin: 0 0 4px 0;
            color: #111827;
            font-weight: 600;
        }
        header p {
            margin: 0;
            color: #6b7280;
            font-size: 12.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        thead th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6b7280;
            background: #f9fafb;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #374151;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) { background: #fafafa; }

        .text-right { text-align: right; }
        .muted { color: #9ca3af; }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .badge-denda { background: #fef2f2; color: #b91c1c; }
        .badge-tepat { background: #f0fdf4; color: #15803d; }

        .summary {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }
        .summary-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 20px;
            min-width: 240px;
            text-align: right;
        }
        .summary-box .label {
            font-size: 11.5px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .summary-box .value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-top: 2px;
        }

        footer {
            margin-top: 40px;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }

        @media print {
            body { padding: 0 24px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
    </div>

    <header>
        <h1>Laporan Pengembalian Alat</h1>
        <p>
            Periode: {{ $dariTanggal ?? 'Semua' }} s/d {{ $sampaiTanggal ?? 'Semua' }}
            &nbsp;&middot;&nbsp; Dicetak {{ now()->format('d-m-Y H:i') }}
        </p>
    </header>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Rencana Kembali</th>
                <th>Tgl Dikembalikan</th>
                <th>Petugas</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $item)
                @php $denda = optional($item->pengembalian)->denda ?? 0; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($item->user)->name ?? '-' }}</td>
                    <td>{{ $item->tgl_kembali_plan }}</td>
                    <td class="{{ optional($item->pengembalian)->tgl_kembali ? '' : 'muted' }}">
                        {{ optional($item->pengembalian)->tgl_kembali ?? '-' }}
                    </td>
                    <td class="{{ optional(optional($item->pengembalian)->petugas)->name ? '' : 'muted' }}">
                        {{ optional(optional($item->pengembalian)->petugas)->name ?? '-' }}
                    </td>
                    <td class="text-right">
                        @if($denda > 0)
                            <span class="badge badge-denda">Rp {{ number_format($denda, 0, ',', '.') }}</span>
                        @else
                            <span class="badge badge-tepat">Tepat waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted" style="text-align:center; padding:24px;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Denda</div>
            <div class="value">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        </div>
    </div>

    <footer>
        Laporan ini dibuat otomatis oleh sistem peminjaman alat.
    </footer>
</body>
</html>