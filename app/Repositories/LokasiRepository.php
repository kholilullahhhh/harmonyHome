<?php

namespace App\Repositories;

use App\Contracts\Repositories\LokasiRepository as LokasiRepositoryContract;
use App\Models\Lokasi;
use App\Repositories\BaseRepository;

class LokasiRepository extends BaseRepository implements LokasiRepositoryContract
{
    public function __construct(Lokasi $model)
    {
        $this->model = $model;
    }
}