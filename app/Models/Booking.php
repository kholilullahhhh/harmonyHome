<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use LogsActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_EXPIRED = 'expired';

    public const TYPE_MEMBER = 'member';

    public const TYPE_GUEST = 'guest';

    protected $fillable = [
        'booking_code',
        'user_id',
        'booking_type',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_identity_number',
        'guest_gender',
        'guest_birth_date',
        'guest_address',
        'kost_id',
        'kamar_id',
        'start_date',
        'end_date',
        'duration_months',
        'price_per_month',
        'subtotal',
        'discount',
        'additional_fee',
        'total_amount',
        'status',
        'notes',
        'access_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'guest_birth_date' => 'date',
        'duration_months' => 'integer',
        'price_per_month' => 'integer',
        'total_amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * Satu booking bisa punya beberapa percobaan bayar (failed/expired),
     * pembayaran aktif adalah yang berstatus pending/paid terbaru.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function isGuest(): bool
    {
        return $this->booking_type === self::TYPE_GUEST;
    }

    public function customerName(): string
    {
        return $this->isGuest() ? ($this->guest_name ?? 'Guest') : ($this->user->name ?? '-');
    }

    public function customerEmail(): ?string
    {
        return $this->isGuest() ? $this->guest_email : ($this->user->email ?? null);
    }

    public function customerPhone(): ?string
    {
        return $this->isGuest() ? $this->guest_phone : ($this->user->phone ?? null);
    }
}
