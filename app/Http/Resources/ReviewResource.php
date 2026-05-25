<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'product_id'  => $this->product_id,
            'rating'      => $this->rating,
            'title'       => $this->title,
            'body'        => $this->body,
            'is_approved' => $this->is_approved,
            'user'        => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
