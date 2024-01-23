<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Services\UserService;
use App\Domains\Booking\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to book',
                'errors' => $validator->errors()
            ]);
        }

        $data = [
            'restaurant_id' => $request->input('restaurant_id'),
            'customer_name' => $request->input('customer_name'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'special_requests' => $request->input('special_requests'),
            'is_online' => $request->input('is_online', false),
        ];

        $createdBooking = $this->bookingService->createBooking($data, $request->user());

        if (!$createdBooking['status']) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to book',
                'errors' => $createdBooking['errors']
            ]);
        }

        // Return a response as needed
    }
}
