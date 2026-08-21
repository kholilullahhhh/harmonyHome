<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Kedaluwarsakan booking pending yang melewati batas waktu pembayaran dan lepaskan kamar';

    public function handle(BookingService $service): int
    {
        $count = $service->expireStale();

        $this->info("{$count} booking kedaluwarsa diproses.");

        return self::SUCCESS;
    }
}
