<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id'                  => $this->id,
            'customer_id'         => $this->customer_id,
            'name'                => trim($this->first_name . ' ' . ($this->last_name ?? '')),
            'email'               => $this->email,
            'phone'               => $this->phone,
            'balance'             => $this->balance,
            'created_on'          => Carbon::parse($this->created_at)->format('d-M, Y'),
            'transactions_count'  => $this->transactions_count,
        ];
    }
}
