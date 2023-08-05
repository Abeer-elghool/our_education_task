<?php

namespace App\Http\Resources\Api\Transaction;

use App\Enums\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'paidAmount' => $this->paidAmount,
            'Currency' => $this->Currency,
            'parentEmail' => $this->parentEmail,
            'statusCode' => TransactionStatus::get_statue($this->statusCode),
            'paymentDate' => Carbon::parse($this->paymentDate)->format('Y-m-d'),
            'parentIdentification' => $this->parentIdentification,
        ];
    }
}
