{{-- resources/views/emails/monthly_sales_summary.blade.php --}}

@component('mail::message')
# Résumé Mensuel des Ventes

Cher {{ $user->name }},

Voici le résumé mensuel des ventes pour le mois en cours.

Total des ventes : {{ number_format($totalSales, 2, ',', ' ') }} FCFA

Merci pour votre excellent travail !

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
