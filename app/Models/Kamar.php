<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kamar extends Model
{
    use LogsActivity, SoftDeletes;

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_RESERVED = 'reserved';

    public const STATUS_OCCUPIED = 'occupied';

    public const STATUS_MAINTENANCE = 'maintenance';

    protected $table = 'kamar';

    protected $fillable = [
        'kost_id',
        'tipe_kamar_id',
        'number',
        'price_monthly',
        'size',
        'floor',
        'description',
        'photo',
        'status',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class);
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kamar_fasilitas');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Status yang boleh dipesan hanya available.
     */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
}
