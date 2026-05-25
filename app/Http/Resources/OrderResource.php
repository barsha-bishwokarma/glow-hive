<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return [
            'id'             => $this->id,
            'status'         => $this->status,
            'total_price'    => $this->total_price,
            'address'        => $this->address,
            'phone'          => $this->phone,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'placed_at'      => $this->created_at,
            'items'          => OrderItemResource::collection($this->whenLoaded('items')),
        ];

        // return parent::toArray($request);
    }
}
