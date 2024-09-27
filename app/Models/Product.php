<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock_quantity', 'media'];

    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'variation_combination_variation')
            ->withPivot('value', 'product_id');
    }

    public function variationCombinations()
    {
        return $this->hasMany(VariationCombination::class);
    }

    public function variation_name(){
        return $this->hasMany(Variation::class);
    }

}
