<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'media' => $this->media,
            'variations' => $this->variation_name->map(function ($variation) {
                return [
                    'id' => $variation->id,
                    'name' => $variation->name
                ];
            }),
            'variation_combinations' => $this->variationCombinations->map(function ($combination) {
                return [
                    'id' => $combination->id,
                    'price' => $combination->price,
                    'stock_quantity' => $combination->stock_quantity,
                    'variations' => $combination->variations->map(function ($variation) {
                        return [
                            'variation_id' => $variation->id,
                            'value' => $variation->pivot->value,
                        ];
                    }),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
