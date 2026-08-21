<?php

namespace App\Repositories;

use App\Contracts\Repositories\PaymentRepository as PaymentRepositoryContract;
use App\Models\Payment;
use App\Repositories\BaseRepository;

class PaymentRepository extends BaseRepository implements PaymentRepositoryContract
{
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }
}