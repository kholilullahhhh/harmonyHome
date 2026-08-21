<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    use LogsActivity;

    protected $table = 'fasilitas';

    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    public function kosts(): BelongsToMany
    {
        return $this->belongsToMany(Kost::class, 'kost_fasilitas');
    }

    public function kamar(): BelongsToMany
    {
        return $this->belongsToMany(Kamar::class, 'kamar_fasilitas');
    }
}
