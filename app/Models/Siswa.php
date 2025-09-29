<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        // 'user_id',
        'nis',
        'email',
        'password',
    ];

    public function kelas()
    {
        // return $this->belongsTo(Kelas::class);
        return $this->belongsToMany(\App\Models\Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')->withPivot('tahun_ajaran_id');
    }
    

    protected $hidden = [
        'password',
    ];
}
