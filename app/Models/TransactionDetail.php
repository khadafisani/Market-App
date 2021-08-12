<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $table = "transaction_details";

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'transaction_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}
