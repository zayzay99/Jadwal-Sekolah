<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GuruAvailability extends Model
{
    protected $fillable = ['guru_id', 'hari', 'jam_mulai', 'jam_selesai'];

    /**
     * Get the full time slot string.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function jam(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['jam_mulai'] . ' - ' . $attributes['jam_selesai'],
        );
    }
}
