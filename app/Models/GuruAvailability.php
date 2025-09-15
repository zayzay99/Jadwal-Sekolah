<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the guru that owns the availability.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
