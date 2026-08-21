<?php

namespace App\Repositories;

use App\Contracts\Repositories\FasilitasRepository as FasilitasRepositoryContract;
use App\Models\Fasilitas;
use App\Repositories\BaseRepository;

class FasilitasRepository extends BaseRepository implements FasilitasRepositoryContract
{
    public function __construct(Fasilitas $model)
    {
        $this->model = $model;
    }
}