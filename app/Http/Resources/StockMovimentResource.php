<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovimentResource extends JsonResource
{
    /**
     * Transforma o recurso em um array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type, // Ex: entrada, saída
            'quantity'    => $this->quantity,
            'product_id'  => $this->product_id,
            'user_id'     => $this->user_id,
        ];
    }
}
