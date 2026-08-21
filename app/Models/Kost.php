<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kost extends Model
{
    use LogsActivity, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'kosts';

    protected $fillable = [
        'user_id',
        'lokasi_id',
        'name',
        'slug',
        'description',
        'address',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'phone',
        'latitude',
        'longitude',
        'rules',
        'access_hours',
        'cover',
        'photos',
        'status',
    ];

    protected $casts = [
        'photos' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kost_fasilitas');
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Harga mulai (kamar termurah) — dihitung dari relasi kamar.
     */
    public function minPrice(): int
    {
        return (int) ($this->kamar()->min('price_monthly') ?: 0);
    }
}
