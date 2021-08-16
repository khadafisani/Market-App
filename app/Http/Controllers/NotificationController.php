<?php

namespace App\Http\Controllers;

use Notification;
use App\Notifications\LowStockNotification;
use App\Models\User;
use App\Models\Product;

class NotificationController extends Controller
{
    public function sendLowStock()
    {
        $users = User::where('role', 'gudang')->get();

        $product = Product::withSum('productIn as stock_in', 'stock_in')->withSum('productOut as stock_out', 'stock_out')->get();

        foreach($product as $result)
        {
            $stock = $result->stock_in - $result->stock_out;
            if($stock < 5)
            {
                $message = [
                    'message' => 'Stock '.$result->name.' with ID '.$result->id.' is less than 5, please RESTOCK the product',
                ];

                Notification::send($users, new LowStock($message));
            }
        }
    }
}
