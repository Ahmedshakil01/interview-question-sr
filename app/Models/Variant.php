<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'title', 'description'
    ];
    public function allvariant() {
        return $this->hasMany(ProductVariant::class, 'variant_id','id')->groupBy('variant')->orderBy('id','ASC');
    }
}
