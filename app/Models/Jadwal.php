<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapel', 'kelas', 'guru_id', 'hari', 'jam', 'kelas_id', 'jadwal_kategori_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kategori()
    {
        return $this->belongsTo(JadwalKategori::class, 'jadwal_kategori_id');
    }
}
