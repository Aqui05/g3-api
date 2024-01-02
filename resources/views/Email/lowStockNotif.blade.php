<x-mail::message>
    # Stock Faible

    Cher vendeur,

    Nous tenons à vous informer que le stock de votre produit " {{$productName}} " est actuellement faible.
    Veuillez prendre les mesures nécessaires pour reconstituer votre stock et éviter toute interruption dans les ventes.

    Vous pouvez accéder à votre tableau de bord pour plus de détails sur le produit en question.

    <x-mail::button :url="'#/lien_vers_votre_tableau_de_bord'">
        Voir le Tableau de Bord
    </x-mail::button>

    Merci de votre attention.

    Cordialement,
    L'équipe de MarketEase.
</x-mail::message>
