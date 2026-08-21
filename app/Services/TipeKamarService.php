<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\TipeKamarRepository;

class TipeKamarService extends BaseService
{
    public function __construct(TipeKamarRepository $repository)
    {
        parent::__construct($repository);
    }
}