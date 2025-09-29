<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\GuruAvailability;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama', 'nip', 'pengampu', 'email', 'password', 'profile_picture', 'total_jam_mengajar', 'sisa_jam_mengajar', 'tahun_ajaran_id',
    ];

    /**
     * Mendapatkan kelas-kelas di mana guru ini menjadi wali kelas.
     */
    public function waliKelas()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    public function availabilities()
    {
        return $this->hasMany(GuruAvailability::class);
    }

    protected $hidden = [
        'password',
    ];

    /**
     * Format total_jam_mengajar into hours and minutes.
     *
     * @return string
     */
    public function getFormattedTotalJamMengajarAttribute()
    {
        $totalMenit = $this->attributes['total_jam_mengajar'];
        if ($totalMenit === null) {
            return '0 jam';
        }
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        
        if ($menit == 0) {
            return $jam . ' jam';
        }
        return $jam . ' jam ' . $menit . ' menit';
    }

    /**
     * Format sisa_jam_mengajar into hours and minutes.
     *
     * @return string
     */
    public function getFormattedSisaJamMengajarAttribute()
    {
        $totalMenit = $this->attributes['sisa_jam_mengajar'];
        if ($totalMenit === null) {
            return '0 jam';
        }
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;

        if ($menit == 0) {
            return $jam . ' jam';
        }
        return $jam . ' jam ' . $menit . ' menit';
    }

    /**
     * Format used_minutes into hours and minutes.
     *
     * @return string
     */
    public function getFormattedUsedMinutesAttribute()
    {
        $totalMenit = $this->attributes['used_minutes'] ?? 0;
        if ($totalMenit === null) {
            return '0 jam';
        }
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        
        if ($menit == 0) {
            return $jam . ' jam';
        }
        return $jam . ' jam ' . $menit . ' menit';
    }
}
