<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'type'                => $this->type,
            'date'                => Carbon::parse($this->created_at)->format('d-M, Y'),
            'amount'              => $this->amount,
            'ip_address'          => $this->ip_address,
        ];
    }
}
