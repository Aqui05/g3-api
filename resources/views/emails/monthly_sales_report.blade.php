@component('mail::message')
# Rapport Mensuel des Ventes

@foreach ($salesData as $seller => $monthlySales)
    ## {{ $seller }}
    | Mois                 | Prix Total des Ventes |
    | -------------------- | ---------------------- |

    @if(is_array($monthlySales) && count($monthlySales) > 0)
        @foreach ($monthlySales as $month => $totalSales)
            | {{ $month }}         | {{ $totalSales }}     |
        @endforeach
    @else
        | Aucune donnée de vente disponible pour ce vendeur |
    @endif
@endforeach

Merci de votre travail acharné!

@endcomponent
