<?php

namespace App\Repositories;

use App\Contracts\Repositories\TipeKamarRepository as TipeKamarRepositoryContract;
use App\Models\TipeKamar;
use App\Repositories\BaseRepository;

class TipeKamarRepository extends BaseRepository implements TipeKamarRepositoryContract
{
    public function __construct(TipeKamar $model)
    {
        $this->model = $model;
    }
}