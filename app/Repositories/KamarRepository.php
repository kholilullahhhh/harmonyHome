<?php

namespace App\Repositories;

use App\Contracts\Repositories\KamarRepository as KamarRepositoryContract;
use App\Models\Kamar;
use App\Repositories\BaseRepository;

class KamarRepository extends BaseRepository implements KamarRepositoryContract
{
    public function __construct(Kamar $model)
    {
        $this->model = $model;
    }
}