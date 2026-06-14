<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primaryImage = $this->product->images->first();

        return [
            'id'       => $this->id,
            'quantity' => $this->quantity,
            'product'  => [
                'id'         => $this->product->id,
                'name'       => $this->product->name,
                'price'      => $this->product->price,
                'sale_price' => $this->product->sale_price,
                'image'      => $primaryImage
                    ? asset('storage/' . $primaryImage->image)
                    : null,
            ],
        ];
    }
}
