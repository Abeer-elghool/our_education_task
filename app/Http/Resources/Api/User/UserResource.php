<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Transaction\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'currency' => $this->currency,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d'),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
