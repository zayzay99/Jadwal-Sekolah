<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapel', 'tabelj_id', 'guru_id', 'hari', 'jam', 'kelas_id', 'jadwal_kategori_id', 'tahun_ajaran_id',
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
