<x-mail::message>
    # Change password request

    Cliquer sur le bouton suivant pour changer votre mot de passe:

    <x-mail::button :url="'password/reset/'.$token">
        Changer de mot de passe
    </x-mail::button>

    Votre code de modification: " {{$resetCode}} "

    Si vous n'avez pas initié cette demande, veuillez ignorer ce mail.

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>
