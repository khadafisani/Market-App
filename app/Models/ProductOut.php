<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductOut extends Model
{
    use HasFactory;

    protected $table = "product_out";

    protected $fillable = [
        'transaction_id',
        'product_in_id',
        'stock_out',
        'cost_history',
    ];

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, 'product_in_id');
    }

    public static function getReport()
    {
        return DB::table('product_out')
        ->select(
                DB::raw('product_out.created_at as Date'),
                'name',
                DB::raw('sum(stock_out) as stock_out'),
                DB::raw('sum(stock_out * purchase_cost) as outcome'),
                DB::raw('sum(stock_out * cost_history) as income'),
            )
        ->join('product_in', 'product_in_id', '=', 'product_in.id')
        ->join('products', 'product_id', '=', 'products.id')
        ->groupBy('product_id');
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class);
    }
}
