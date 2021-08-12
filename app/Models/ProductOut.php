<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOut extends Model
{
    use HasFactory;

    protected $table = "product_out";

    protected $fillable = [
        'transaction_id',
        'product_in_id',
        'stock_out',
    ];

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, 'product_in_id');
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class);
    }
}
