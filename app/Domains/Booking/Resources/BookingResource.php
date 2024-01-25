<?php

namespace App\Domains\Booking\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    protected $status;
    protected $message;

    public function setStatus(int $status): BookingResource
    {
        $this->status = $status;
        return $this;
    }

    public function setMessage(string $message): BookingResource
    {
        $this->message = $message;
        return $this;
    }

    public function with($request)
    {
        return [
            'status'  => $this->status ?? 200,
            'message' => $this->message ?? 'Successfully created booking',
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
