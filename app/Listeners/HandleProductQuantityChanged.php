<?php

namespace App\Listeners;

use App\Events\ProductUpdated;
use App\Events\ProductQuantityChanged;
    use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
    use App\Mail\LowStockNotificationMail;
    use Illuminate\Support\Facades\Mail;

class HandleProductQuantityChanged
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */

public function handle(ProductQuantityChanged $event)
{
    $product = $event->product;

    if ($product->quantity < 10) {
        if ($product->user) {
            $sellerEmail = $product->user->email;
            // Envoyer un e-mail au vendeur
            $productName = $product->name;

        Mail::to($sellerEmail)->send(new LowStockNotificationMail($productName));}
    }
}
}
