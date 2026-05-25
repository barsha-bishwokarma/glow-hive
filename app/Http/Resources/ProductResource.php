<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;

class ProductResource extends JsonResource
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
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'image'          => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'price'          => $this->price,
            'sale_price'     => $this->sale_price,
            'stock_quantity' => $this->stock_quantity,
            'is_featured'    => $this->is_featured,
            'average_rating' => $this->average_rating,
            'review_count'   => $this->review_count,
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'brand'          => new BrandResource($this->whenLoaded('brand')),
        ];

        // return parent::toArray($request);
    }
}
