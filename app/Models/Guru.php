<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama', 'nip', 'pengampu', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];
}
