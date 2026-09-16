<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    // Menambahkan alias plural untuk mencocokkan dengan Controller/View
    public function detailPinjams(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function getTglPinjamFormattedAttribute()
    {
        return Carbon::parse($this->attributes['tgl_pinjam'])->translatedFormat('d F Y H:i');
    }

    public function getTglKembaliPlanFormattedAttribute()
    {
        return Carbon::parse($this->attributes['tgl_kembali_plan'])->translatedFormat('d F Y H:i');
    }
}