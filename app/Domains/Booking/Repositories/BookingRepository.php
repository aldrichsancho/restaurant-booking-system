<?php

namespace App\Domains\Booking\Repositories;

use App\Models\Booking;

class BookingRepository
{
    public function createBooking(array $data): Booking
    {
        return Booking::create($data);
    }
}
