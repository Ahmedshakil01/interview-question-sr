<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $guarded = [];

    public function firstVariation() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one','id')->withDefault();
    }

    public function secondVariation() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two','id')->withDefault();
    }

    public function thirdVariation() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three','id')->withDefault();
    }

}
