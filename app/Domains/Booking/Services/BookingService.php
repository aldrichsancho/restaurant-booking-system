<?php

namespace App\Domains\Booking\Services;

use App\Domains\Auth\Services\UserService;
use App\Domains\Booking\Repositories\BookingRepository;
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

        // Use a database transaction to handle racing conditions
        DB::transaction(function () use ($data) {
            $this->bookingRepository->createBooking($data);
            return ['status' => true];
        });

        return [
            'status' => false,
            'errors' => 'Something went wrong or someone booked your table'
        ];
    }
}
