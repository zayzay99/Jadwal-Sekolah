<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabelj extends Model
{
    use HasFactory;

    protected $fillable = ['jam', 'jam_mulai', 'jam_selesai', 'jadwal_kategori_id'];

    public $timestamps = false;

    public function jadwalKategori()
    {
        return $this->belongsTo(JadwalKategori::class);
    }
}
