<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    
        public function index()
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_alat'          => Alat::count(),
            'total_kategori'      => Kategori::count(),
            'total_user'          => User::count(),
            'peminjaman_aktif'    => Peminjaman::where('status', 'dipinjam')->count(),
            'menunggu_persetujuan'=> Peminjaman::where('status', 'diajukan')->count(),
            'denda_bulan_ini'     => \App\Models\Pengembalian::whereMonth('tgl_kembali', now()->month)
                                        ->whereYear('tgl_kembali', now()->year)
                                        ->sum('denda'),
        ];

        // Tren pengajuan peminjaman 7 hari terakhir, untuk grafik
        $tren = collect();
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $tren->push([
                'label'  => $tanggal->translatedFormat('d M'),
                'jumlah' => Peminjaman::whereDate('created_at', $tanggal->toDateString())->count(),
            ]);
        }

        // 5 alat paling sering dipinjam
        $alatTerpopuler = DetailPinjam::selectRaw('alat_id, SUM(jumlah) as total')
            ->with('alat')
            ->groupBy('alat_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('logs', 'stats', 'tren', 'alatTerpopuler'));
    }
    
    // Menampilkan Halaman Khusus Log Aktivitas (BARU)
    public function indexLogAktivitas(Request $request)
    {
        $search = $request->input('search');

        $logs = LogAktivitas::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('aktivitas', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.log-aktivitas.index', compact('logs', 'search'));
    }

    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    public function storeAlat(Request $request)
    {
               $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:1',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'stok.min' => 'Stok alat minimal harus 1, tidak boleh 0.',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat = Alat::create($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan data alat baru: ' . $alat->nama_alat,
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui data alat: ' . $alat->nama_alat,
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $namaAlat = $alat->nama_alat;

        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus data alat: ' . $namaAlat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil dihapus.');
    }

        public function indexUser(Request $request)
    {
        $search = $request->input('search');

                $users = User::when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('no_hp', 'like', '%' . $search . '%');
            });
        })
            ->orderByRaw("FIELD(role, 'admin', 'petugas', 'peminjam')")
            ->orderBy('name')
            ->get();

        return view('admin.user.index', compact('users', 'search'));
    }
    
    public function createUser()
    {
        return view('admin.user.create');
    }

       public function editUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_super_admin && $user->id !== auth()->id()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Data admin utama tidak bisa diedit oleh admin lain.');
        }

        return view('admin.user.edit', compact('user'));
    }
    
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan kategori baru: ' . $kategori->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui kategori: ' . $kategori->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus kategori: ' . $namaKategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function indexPeminjaman(Request $request)
    {
        $query = Peminjaman::with('user', 'detailPinjams.alat')
            ->where('status', '!=', 'selesai'); 

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                ->orWhereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        }

        $peminjamans = $query->latest()->paginate(10);

        return view('admin.peminjaman.index', compact('peminjamans'));
    }

    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            DB::commit();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menambahkan transaksi peminjaman baru (ID #' . $peminjaman->id . ')',
            ]);

            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $request->validate([
            // Pastikan 'selesai' ada di dalam daftar validasi ini:
            'status' => 'required|in:diajukan,dipinjam,selesai,telat,dikembalikan',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && ($statusBaru == 'selesai' || $statusBaru == 'dikembalikan' || $statusBaru == 'telat')) {
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }

                $rencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
                $sekarang = now();
                $denda = 0;

                if ($sekarang->gt($rencana)) {
                    $hariTelat = (int) ceil($rencana->diffInDays($sekarang));
                    $denda = max(1, $hariTelat) * 10000;
                }

                \App\Models\Pengembalian::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'tgl_kembali' => $sekarang->toDateString(),
                        'kondisi_kembali' => 'Baik',
                        'denda' => $denda,
                        'petugas_id' => auth()->id(),
                    ]
                );
            }

            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();

            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);

        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjamanId = $peminjaman->id;
        $peminjaman->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus data peminjaman (ID #' . $peminjamanId . ')',
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function indexPengembalian(Request $request)
    {
        $query = Peminjaman::with('user', 'detailPinjams.alat', 'pengembalian')
            ->whereIn('status', ['selesai', 'telat', 'dikembalikan']); // Hanya ambil yang sudah selesai / telat

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', "%{$search}%");
            });
        }

        $peminjamans = $query->latest()->paginate(10);

        return view('admin.pengembalian.index', compact('peminjamans'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        // Hitung keterlambatan otomatis (Rp 10.000 / hari)
        $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $sekarang = \Carbon\Carbon::now();
        
        $dendaTelat = 0;
        $statusBuku = 'selesai';

        if ($sekarang->gt($tglRencana)) {
            $selisihHari = (int) ceil($tglRencana->diffInDays($sekarang));
            if ($selisihHari < 1) $selisihHari = 1;
            $dendaTelat = $selisihHari * 10000;
            $statusBuku = 'telat';
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }

            // Update status peminjaman
            $peminjaman->update(['status' => $statusBuku]);

            // Simpan ke tabel pengembalian
            \App\Models\Pengembalian::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'tgl_kembali' => $sekarang->toDateString(),
                    'kondisi_kembali' => $request->input('kondisi_kembali', 'baik'),
                    'denda' => $dendaTelat, // Denda otomatis 10.000/hari
                    'petugas_id' => auth()->id(),
                ]
            );

            DB::commit();
            return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian alat berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

        public function laporanPengembalian(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');
        $search = $request->input('search');

        $query = Peminjaman::with('user', 'detailPinjams.alat', 'pengembalian.petugas')
            ->whereIn('status', ['selesai', 'telat', 'dikembalikan']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereHas('pengembalian', function ($q) use ($dariTanggal, $sampaiTanggal) {
                $q->whereBetween('tgl_kembali', [$dariTanggal, $sampaiTanggal]);
            });
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $peminjamans = $query->latest()->get();
        $totalDenda = $peminjamans->sum(fn ($item) => optional($item->pengembalian)->denda ?? 0);

        return view('admin.laporan.index', compact('peminjamans', 'totalDenda', 'dariTanggal', 'sampaiTanggal', 'search'));
    }

        public function destroyPengembalian($id)
    {
        $peminjaman = Peminjaman::with('detailPinjams', 'pengembalian')->findOrFail($id);
        $namaPeminjam = optional($peminjaman->user)->name ?? '-';

        DB::beginTransaction();
        try {
            optional($peminjaman->pengembalian)->delete();
            $peminjaman->detailPinjams()->delete();
            $peminjaman->delete();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menghapus riwayat pengembalian peminjaman #' . $id . ' (' . $namaPeminjam . ')',
            ]);

            DB::commit();
            return redirect()->route('admin.pengembalian.index')->with('success', 'Riwayat pengembalian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function cetakLaporanPengembalian(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Peminjaman::with('user', 'detailPinjams.alat', 'pengembalian.petugas')
            ->whereIn('status', ['selesai', 'telat', 'dikembalikan']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereHas('pengembalian', function ($q) use ($dariTanggal, $sampaiTanggal) {
                $q->whereBetween('tgl_kembali', [$dariTanggal, $sampaiTanggal]);
            });
        }

        $peminjamans = $query->latest()->get();
        $totalDenda = $peminjamans->sum(fn ($item) => optional($item->pengembalian)->denda ?? 0);

        return view('admin.laporan.cetak', compact('peminjamans', 'totalDenda', 'dariTanggal', 'sampaiTanggal'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/user'), $filename);
            $fotoPath = 'storage/user/' . $filename;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'foto' => $fotoPath,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan user baru: ' . $user->name,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path($user->foto))) {
                unlink(public_path($user->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/user'), $filename);
            $data['foto'] = 'storage/user/' . $filename;
        }

        $user->update($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui data user: ' . $user->name,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

        public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->is_super_admin) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Admin utama tidak bisa dihapus.');
        }

        if ($user->foto && file_exists(public_path($user->foto))) {
            unlink(public_path($user->foto));
        }

        $namaUser = $user->name;
        $user->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus user: ' . $namaUser,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}