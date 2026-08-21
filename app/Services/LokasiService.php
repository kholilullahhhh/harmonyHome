<?php

namespace App\Services;

use App\Repositories\LokasiRepository;
use Illuminate\Support\Str;

class LokasiService extends BaseService
{
    public function __construct(LokasiRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return parent::create($data);
    }

    public function update(int $id, array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        return parent::update($id, $data);
    }
}
