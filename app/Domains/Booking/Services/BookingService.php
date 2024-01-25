<?php

namespace App\Domains\Booking\Services;

use App\Domains\Auth\Services\UserService;
use App\Domains\Booking\Repositories\BookingRepository;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $bookingRepository;
    protected $userService;

    public function __construct(BookingRepository $bookingRepository, UserService $userService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->userService = $userService;
    }

    public function getAllBookings(int $perPage, int $pageNumber) {
        return $this->bookingRepository->getAll($perPage, $pageNumber);
    }

    public function createBooking(array $data, User $user): array
    {
        $customer_name = $data['customer_name'];

        if ($customer_name == '' || $customer_name == null) {
            $user = $this->userService->findUserById($user->id);
            if ($data['is_online'] && $user != null) {
                $customer_name = $user->name;
            } else {
                return [
                    'status' => false,
                    'errors' => 'Customer name cannot be empty'
                ];
            }
        }

        if (!$data['is_online']) $data['status'] = 'confirmed';

        // Use a database transaction to handle racing conditions
        DB::beginTransaction();
        try {
            $checkAvaiablity = $this->bookingRepository->checkAvailability(
                $data['restaurant_id'],
                $data['table_id'],
                $data['start_time'],
                $data['end_time']
            );

            if (!$checkAvaiablity) {
                return [
                    'status' => false,
                    'errors' => 'Someone booked the table'
                ];
            }

            $booking = $this->bookingRepository->createBooking($data);
            DB::commit();
            return ['status' => true, 'data' => $booking];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status' => false,
                'errors' => 'Something went wrong or someone booked the table'
            ];
        }
    }

    public function updateBookingStatus(string $status, string $id): Booking
    {
        return $this->bookingRepository->update(['status' => $status], $id);
    }
}
