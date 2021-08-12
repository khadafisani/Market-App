<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "product_in";

    protected $fillable = [
        'product_id',
        'stock_in',
        'price',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productOut()
    {
        return $this->hasMany(ProductOut::class, 'product_in_id', 'id');
    }
}
