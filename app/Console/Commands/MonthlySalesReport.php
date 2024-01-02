<?php

namespace App\Console\Commands;

use App\Mail\MonthlySalesReportMail;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MonthlySalesReport extends Command
{
    protected $signature = 'monthly-sales:report';
    protected $description = 'Send monthly sales report to vendors';

    public function handle()
    {
        // Obtenez la date actuelle
        $currentMonth = Carbon::now()->startOfMonth();

        // Obtenez les données des ventes par mois pour chaque vendeur
        $salesData = User::where('role', 'seller')
            ->with(['orders' => function ($query) use ($currentMonth) {
                $query->whereMonth('created_at', $currentMonth->month);
            }])
            ->get()
            ->mapWithKeys(function ($user) {
                $monthlySales = $user->orders->sum('total');
                return [$user->email => $monthlySales];
            });

        // Envoyez l'e-mail
        Mail::to('kikissagbeaquilas@gmail.com')->send(new MonthlySalesReportMail($salesData));
    }

    /**
     * Execute the console command.
     */
}
