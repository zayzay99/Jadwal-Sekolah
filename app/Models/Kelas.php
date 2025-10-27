<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'guru_id', 'tahun_ajaran_id'];

    /**
     * Mendapatkan tahun ajaran tempat kelas ini berada.
     */
    public function tahunAjaran() {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'kelas_id', 'siswa_id')->withPivot('tahun_ajaran_id');
    }
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    // app/Models/Kelas.php
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
    public function siswasOrdered()
{
    return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'kelas_id', 'siswa_id')
                ->withPivot('tahun_ajaran_id')
                ->orderBy('nama', 'asc');
}
}
