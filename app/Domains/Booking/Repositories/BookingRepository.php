<?php

namespace App\Domains\Booking\Repositories;

use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingRepository
{
    public function getAll(int $perPage, int $pageNumber): LengthAwarePaginator {
        return Booking::paginate($perPage, ['*'], 'page', $pageNumber);
    }

    public function checkAvailability(
        string $restaurant_id,
        string $table_id,
        string $start_time,
        string $end_time
    ): bool
    {
        return Booking::where('restaurant_id', $restaurant_id)
                    ->where('table_id', $table_id)
                    ->where(function($query) use ($start_time, $end_time) {
                        $query->whereBetween('start_time', [$start_time, $end_time])
                            ->orWhereBetween('end_time', [$start_time, $end_time])
                            ->orWhere(function($query) use ($start_time, $end_time) {
                                $query->where('start_time', '<', $start_time)
                                    ->where('end_time', '>', $end_time);
                            });
                    })
                    ->count() < 1;
    }

    public function createBooking(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(array $data, string $id): Booking
    {
        Booking::find($id)->update($data);
        return Booking::find($id);
    }
}
