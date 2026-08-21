<?php

namespace App\Repositories;

use App\Contracts\Repositories\KostRepository as KostRepositoryContract;
use App\Models\Kost;
use App\Repositories\BaseRepository;

class KostRepository extends BaseRepository implements KostRepositoryContract
{
    public function __construct(Kost $model)
    {
        $this->model = $model;
    }
}