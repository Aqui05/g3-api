<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Facade;
use App\Models\User;
use DeepCopy\f001\B;
use Faker\Core\Number;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;


class UserController extends Controller
{
    //

    public function becomeSeller(Request $request)
    {
        $user=Auth::user();

        if($user->role === 'seller')
        {
            return response()->json(['message'=>'Vous êtes déja un vendeur']);

        }$validator =Validator::make(
            $request->all(),[
                'company'=>'required|string',
                'phone_number'=>'required|string',
                'numeroIDCard_Passport' => 'required|string',
                ]
            );
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }

        $user->update([
            'role'=>'seller',
            'phone_number'=>($request->phone_number),
            'company'=>($request->company),
            'numeroIDCard_Passport' => $request->numeroIDCard_Passport,
            'imgIDCard_Passport' => $request->imgIDCard_Passport,
            'Description_boutique' => $request->Description_boutique,
            'Emplacement_boutique' => $request->Emplacement_boutique,
            'photo_coverage' => $request->photo_coverage,
            ]);
        return response()->json(['message'=>'Vous êtes maintenant un vendeur']);
    }

    public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $email = $request->input('email');
    $token = $this->generateToken($email);
    $resetCode = $this->generateCode($email);

    // Envoi du courriel avec le lien de réinitialisation
    Mail::to($email)->send(new ResetPasswordMail($token,$resetCode));

    return response()->json(['message' => 'Reset email sent successfully']);
}

public function generateCode($email)
{

    // Vérifier si l'e-mail existe dans la base de données
    $userExists = DB::table('users')
        ->where('email', $email)
        ->exists();

    if (!$userExists) {
        // Retourner une réponse, lever une exception, etc.
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Rechercher si un token existe déjà pour cet utilisateur
    $existingCode = DB::table('password_reset_tokens')
        ->where('email', $email)
        ->first();

    // Générer un code de réinitialisation à 5 chiffres
    $resetCode = mt_rand(10000, 99999);

    if ($existingCode) {
        // Si un token existe déjà, le mettre à jour avec le nouveau token et le code
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'reset_code' => $resetCode,
                'created_at' => now(),
            ]);
    } else {
        // Si aucun token n'existe, insérez-en un nouveau avec le code
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'reset_code' => $resetCode,
            'created_at' => now(),
        ]);
    }
}

public function generateToken($email)
{

    // Vérifier si l'e-mail existe dans la base de données
    $userExists = DB::table('users')
        ->where('email', $email)
        ->exists();

    if (!$userExists) {
        // Retourner une réponse, lever une exception, etc.
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Rechercher si un token existe déjà pour cet utilisateur
    $existingToken = DB::table('password_reset_tokens')
        ->where('email', $email)
        ->first();

    // Générer un nouveau token
    $token = Str::random(60);

    if ($existingToken) {
        // Si un token existe déjà, le mettre à jour avec le nouveau token et le code
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => $token,
                'created_at' => now(),
            ]);
    } else {
        // Si aucun token n'existe, insérez-en un nouveau avec le code
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }
}


public function resetPassword($token, Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
        'reset_code' => 'required|digits:5',  // New validation rule for 5-digit code
    ]);

    // Rechercher le token dans la base de données
    $passwordReset = DB::table('password_reset_tokens')
        ->where('token', $token)
        ->first();

    // Vérifier si le token existe
    if (!$passwordReset) {
        // Token invalide, rediriger ou renvoyer une réponse d'erreur
        return response()->json(['message' => 'Token invalide'], 404);
    }

    if ($passwordReset->reset_code != $request->input('reset_code')) {
        // Code invalide, rediriger ou renvoyer une réponse d'erreur
        return response()->json(['message' => 'Code de réinitialisation invalide'], 400);
    }

    // Vérifier si le token n'a pas expiré
    if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
        // Token expiré, rediriger ou renvoyer une réponse d'erreur
        return response()->json(['message' => 'Token expiré'], 404);
    }

    // Vous pouvez maintenant réinitialiser le mot de passe pour l'utilisateur associé à ce token
    $email = $request->input('email');
    $password = bcrypt($request->input('password')); // Utilisez bcrypt pour hacher le mot de passe

    // Réinitialiser le mot de passe
    DB::table('users')
        ->where('email', $email)
        ->update(['password' => $password]);

    // Effacer le token de la table après utilisation
    DB::table('password_reset_tokens')->where('token', $token)->delete();

    // Rediriger ou renvoyer une réponse de succès
    return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
}




    public function modifyProfile(Request $request)
    {
        $user=Auth::user();

        $validator =Validator::make(
            $request->all(),[
                'name'=>'required|string',
                'email'=>'required|string|email|unique:users',
                ]
            );
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }

        $user->update([
            'email'=>($request->email),
            'name'=>($request->name),
            ]);
        return response()->json(['user'=>$user]);
    }





    // See all notification

public function seeNotification()
{
    // Get the authenticated user
    $user = Auth::user();

    // Check if a user is authenticated
    if ($user) {
        // Retrieve and display the title and content of each notification
        $notifications = $user->notifications->map(function ($notification) {
            return [
                'title' => $notification->data['title'],
                'content' => $notification->data['content'],
            ];
        });

        dd($notifications);
    } else {
        // Handle the case where no user is authenticated
        return response()->json(['message' => 'User not authenticated'], 401);
    }
}

}


