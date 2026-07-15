<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'comment' => $this->comment,
            'status' => $this->status->value,
            'total_quantity' => $this->total_quantity,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource(
                $this->whenLoaded('user')
            ),
            'client' => new ClientResource(
                $this->whenLoaded('client')
            ),
            'positions' => OrderPositionResource::collection(
                $this->whenLoaded('positions')
            ),
        ];
    }
}
