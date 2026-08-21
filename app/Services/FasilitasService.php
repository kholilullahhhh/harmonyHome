<?php

namespace App\Services;

use App\Repositories\FasilitasRepository;
use Illuminate\Support\Str;

class FasilitasService extends BaseService
{
    public function __construct(FasilitasRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        return parent::create($data);
    }

    public function update(int $id, array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        return parent::update($id, $data);
    }
}
