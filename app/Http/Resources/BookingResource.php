<?php

namespace App\Http\Resources;

use App\Bookings;
use Illuminate\Http\Resources\Json\Resource;

class BookingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'status' => Bookings::$STATUS[$this->status],
            'status_key' => $this->status,
            'rent' => $this->rent,
            'security' => $this->security,
            'installments' => $this->installments,
            'paidSecurity' => round($this->paidSecurity),
            'refunedSecurity' => round($this->refunedSecurity),
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'receipts' => isset($this->receipts) ? ReceiptResource::collection($this->receipts): [],
            'user' => isset($this->user) ? UserResource::make($this->user): null,
            'room' => isset($this->room) ? RoomResource::make($this->room): null,
            'nook' => isset($this->nook) ? NooksResource::make($this->nook): null,
        ];
    }
}
    