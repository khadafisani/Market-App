<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'selling_cost',
    ];

    public function productIn()
    {
        return $this->hasMany(ProductIn::class);
    }

    public function productOut()
    {
        return $this->hasManyThrough(ProductOut::class, ProductIn::class, 'product_id', 'product_in_id', 'id', 'id');
    }

    public function productInWithTrashed()
    {
        return $this->hasMany(ProductIn::class)->withTrashed()->withSum('ProductOut', 'stock_out');
    }

    public function productOutWithTrashedParent()
    {
        return $this->hasManyThrough(ProductOut::class, ProductIn::class, 'product_id', 'product_in_id', 'id', 'id')->withTrashedParents();
    }
}
