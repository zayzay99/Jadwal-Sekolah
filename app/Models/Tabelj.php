<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabelj extends Model
{
    //

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
