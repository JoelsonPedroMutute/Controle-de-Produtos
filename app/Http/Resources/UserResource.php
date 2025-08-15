<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'             => $this->id,
            'created_at'     => $this->created_at,
            'email'          => $this->email,
            'role'           => $this->role,
            'status'        => $this->status,         
            // Relacionamento com StockMovements
            'stock_movements' => StockMovimentResource::collection(
                $this->whenLoaded('stockMovements')
            ),
        ];
    }
}
