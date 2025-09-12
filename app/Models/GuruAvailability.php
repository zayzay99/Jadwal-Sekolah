<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruAvailability extends Model
{
    protected $fillable = ['guru_id', 'hari', 'jam'];
}
