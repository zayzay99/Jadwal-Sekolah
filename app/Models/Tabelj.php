<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabelj extends Model
{
    use HasFactory;

    protected $fillable = ['jam', 'jam_mulai', 'jam_selesai'];

    public $timestamps = false;
}
