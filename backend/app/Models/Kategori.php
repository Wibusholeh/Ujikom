<?php

namespace App\Models;

use App\Models\Alat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $fillable = ['nama_kategori'];

    public function alat(): HasMany {
        return $this->hasMany(Alat::class);
    }

    public function alats()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}