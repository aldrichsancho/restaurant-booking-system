<?php

namespace App\Http\Controllers;

use App\Domains\Booking\Resources\BookingCollection;
use App\Domains\Booking\Resources\BookingResource;
use App\Domains\Booking\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request): BookingCollection | JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return commonResponse(400, 'Failed to fetch', ['errors' => $validator->errors()]);
        }

        $data = $this->bookingService->getAllBookings(
            $request->get('role'),
            $request->get('per_page') ?? 10,
            $request->get('page_number') ?? 1
        );

        return new BookingCollection($data);
    }

    public function store(Request $request): BookingResource | JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required',
            'table_id'      => 'required',
            'start_time'    => [
                'required',
                'date',
                'after_or_equal:' . now()->toDateTimeString(), // Ensures the start_time is after or equal to the current time
            ],
            'end_time'      => [
                'required',
                'date',
                'after:start_time', // Ensures the end_time is after the start_time
            ],
        ]);

        if ($validator->fails()) {
            return commonResponse(400, 'Failed to book', ['errors' => $validator->errors()]);
        }

        $data = [
            'user_id' => Auth::user()->id,
            'restaurant_id' => $request->input('restaurant_id'),
            'table_id' => $request->input('table_id'),
            'customer_name' => $request->input('customer_name'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'special_requests' => $request->input('special_requests'),
            'is_online' => $request->input('is_online', false),
        ];

        $createdBooking = $this->bookingService->createBooking($data, $request->user());

        if (!$createdBooking['status']) {
            return commonResponse(400, 'Failed to book', ['errors' => $createdBooking['errors']]);
        }

        return new BookingResource($createdBooking['data']);
    }

    public function update(Request $request, string $id): BookingResource
    {
        return (new BookingResource(
            $this->bookingService->updateBookingStatus('confirmed', $id)
        ))->setMessage('Successfully confirmed booking');
    }

    public function destroy(string $id): JsonResponse
    {
        $this->bookingService->updateBookingStatus('cancelled', $id);

        return commonResponse(200, 'Successfully cancel booking');
    }
}
