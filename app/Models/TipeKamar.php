<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    use LogsActivity;

    protected $table = 'tipe_kamars';

    protected $fillable = [
        'name',
        'description',
    ];

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }
}
