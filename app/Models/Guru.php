<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama', 'nip', 'pengampu', 'email', 'password', 'profile_picture', 'total_jam_mengajar', 'sisa_jam_mengajar',
    ];

    /**
     * Mendapatkan kelas-kelas di mana guru ini menjadi wali kelas.
     */
    public function waliKelas()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    protected $hidden = [
        'password',
    ];
}
