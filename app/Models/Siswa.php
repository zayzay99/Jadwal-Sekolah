<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'nis',
        'kelas',
        'email',
        'password',
        'kelas_id',
    ];

    public function kelas()
    {
        // return $this->belongsTo(Kelas::class);
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id');
    }

    protected $hidden = [
        'password',
    ];
}
