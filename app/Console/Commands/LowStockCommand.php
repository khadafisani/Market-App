<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Events\LowStockEvent;
use App\Notifications\LowStockNotification;

class LowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lowstock:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Low Stock Notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $product = Product::withSum('productOut as stock_out', 'stock_out')->withSum('productIn as stock_in', 'stock_in')->get();   //get product data

        foreach($product as $product)
        {
            $totalStock = $product->stock_in - $product->stock_out; //get total stock

            if($totalStock < 5) //if stock less than 5, then trigger notification event
            {
                $message = [
                    'message' => 'Stock '.$product->name.' with ID '.$product->id.' is less than 5, please RESTOCK the product',
                ];

                event(new LowStockEvent($message));
            }
        }
    }
}
