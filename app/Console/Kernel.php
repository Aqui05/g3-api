<?php

namespace App\Console;

use App\Models\Product;
use App\Models\Promotion;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        // Code à exécuter à la fin de la promotion

        // Récupérez toutes les promotions qui ont atteint leur date de fin
        $promotions = Promotion::where('end_date', '<=', now())->get();

        foreach ($promotions as $promotion) {
            // Ramenez le prix du produit à la normale
            $product = $promotion->product;
            $discount = $promotion->discount_percent / 100;
            $newPrice = $product->prix / (1 - $discount);

            // Mettez à jour le prix du produit
            $product->update(['prix' => $newPrice]);

            // Supprimez la promotion du produit
            $product->promotions()->delete();
        }

    })->dailyAt('23:59:59');
}

}
