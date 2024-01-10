<x-mail::message>
    # Change password request

        <!-- Assuming your reset-password email template is in Blade syntax -->
    <p>Cliquer sur le bouton suivant pour changer votre mot de passe: </p>
    <a href="{{ url('#/password/reset/'.$token) }}">Reset Password</a>

    <p> Votre code de modification: {{ $resetCode }}</p>

    Si vous n'avez pas initié cette demande, veuillez ignorer ce mail.

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>
