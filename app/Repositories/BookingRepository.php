<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookingRepository as BookingRepositoryContract;
use App\Models\Booking;
use App\Repositories\BaseRepository;

class BookingRepository extends BaseRepository implements BookingRepositoryContract
{
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }
}