<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Events\LowStockEvent;
use App\Notifications\LowStockNotification;
use Illuminate\Notifications\Notification;

class LowStockListener
{
    public function __construct()
    {
        //
    }

    public function handle(LowStockEvent $event)
    {
        $gudang = User::where('role', 'gudang')->get();
        foreach($gudang as $gudang)
        {
            $gudang->notify((new LowStockNotification($event->message))->delay(now()->addMinutes(30)));
        }
    }
}
