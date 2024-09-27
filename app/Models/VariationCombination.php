<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationCombination extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variation_id', 'price', 'stock_quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'variation_combination_variation')
            ->withPivot('value');
    }
}
