<x-mail::message>
    # Change password request

    Cliquer sur le bouton suivant pour changer votre mot de passe.

    <x-mail::button :url="url('#/page de reinitialisation de pwd', ['token' => $token])">
        Reset Password
    </x-mail::button>

    Si vous n'avez pas initié cette demande, veuillez ignorer ce mail.

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>
