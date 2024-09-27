<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function combinations()
    {
        return $this->hasMany(VariationCombination::class);
    }
}
