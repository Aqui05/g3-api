<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DeepCopy\f001\B;
use Faker\Core\Number;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{

    public function _construct()
    {
        $this->middleware('auth:api',['except'=>['login','register']]);
    }

    //fonction pour enregister un acheteur dans la BD
    public function registerBuyer(Request $request)
    {
        $validator =Validator::make(
            $request->all(),[
                'name'=>'required',
                'email'=>'required|string|email|unique:users',
                'password'=>'required|string|confirmed|min:6',
            ]
            );
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            $user = User::create(array_merge(
                $validator->validated(),
                ['password'=>bcrypt($request->password),
                'role' => 'buyer',
                ]
            ));
            return response()->json([
                'message'=>'user successfully registered',
                'user'=>$user
            ],201);
    }

    //fonction pour enregister un vendeur dans la BD
    public function registerSeller(Request $request)
    {
        $validator =Validator::make(
            $request->all(),[
                'name'=>'required',
                'email'=>'required|string|email|unique:users',
                'password'=>'required|string|confirmed|min:6',
                'company'=>'required|string',
                'phone_number'=>'required|string',
                ]
            );
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            $user = User::create(array_merge(
                $validator->validated(),
                ['password'=>bcrypt($request->password),
                'role' => 'seller',
                ]
            ));
            return response()->json([
                'message'=>'user successfully registered',
                'user'=>$user
            ],201);
    }


    //Connexion
    public function login(Request $request)
    {
        $validator =Validator::make(
            $request->all(),[
                'email'=>'required|email',
                'password'=>'required|string|min:6',
            ]
            );
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            if(!$token=auth()->attempt($validator->validated())){
                return response()->json(['error'=>'Email ou mot de passe incorrect'],401);
            }
            return $this->createNewToken($token);
    }

    //Voir le profile
    public function profile() {
        return response()->json(auth()->user());
    }

    //Se déconnecter
    public function logout(){
        auth()->logout();
        return response()->json([
                'message'=>'user logged out ',
            ]);
    }

    //Rafraichir et avoir un nouveau token
     public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }


    //Utliser lors de la création du token
     protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user'=>auth()->user()
        ]);
    }




  /*  public function redirect_github()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback_github()
    {
        $user = Socialite::driver('github')->user();
        dd($user);
    }

    public function redirect_google()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback_google()
    {
        $user = Socialite::driver('google')->user();
        dd($user);
    } */

}
