<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try{
            $SocialUser = Socialite::driver($provider)->user();
            if(User::where('email',$SocialUser->getEmail())->exists())
            {
                return redirect('/login')->withErrors(['email'=>'This email uses different method to login.']);
            }
            $user = User::where([
        'provider_id' => $SocialUser->id,
        'provider' =>$provider
    ])->first();
    if(!$user)
    {
        $user = User::create([
            'name' => $SocialUser->getName(),
            'email' =>$SocialUser->getEmail(),
            'username' => User::generateUsername($SocialUser->getNickname()),
            'password' => Hash::make($SocialUser->getName().'@'.$SocialUser->getId()),
            'provider' => $provider,
            'provider_id' => $SocialUser->getId(),
            'provider_token' => $SocialUser->token,
            'email_verified_at' =>now()
        ]);
    }
    Auth::login($user);

    return redirect('/dashboard');

        }catch(\Exception $e){
            return redirect('/login');
        }


    }

    public function callbacktwo()
    {
        try{
            $user = Socialite::driver('google')->user();

            $is_user = User::where('email',$user->getEmail())->first();

            if(!$is_user){
                $saveUser = User::updateOrCreate(
                    [
                        'provider_id' => $user->getId(),
                    ],
                    [
                        'name' => $user->getName(),
                        'email' => $user->getEmail(),
                        'password' => Hash::make($user->getName().'@'.$user->getId()),
                    ]
                    );
            }
            else{
                $saveUser = User::where('email',$user->getEmail())->update([
                    'provider_id' => $user->getId(),
                ]);
                $saveUser = User::where('email', $user->getEmail())->first();
            }
            Auth::login($saveUser);

            return redirect('/dashboard');
        }
        catch(\Throwable $th)
        {
            throw $th;
        }
    }
}
