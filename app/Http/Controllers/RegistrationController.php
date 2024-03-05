<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Auth\Events\Registered;
class RegistrationController extends Controller
{
    public function signup(Request $request){


        $userData  = $request->validate([
              
             "name" => ["required","string"],
             "email" => ["required","email"],
             "password" => ["required","string","confirmed"],
        ]);


       

            $newUser = User::create([

                      "name" =>$userData['name'],
                      "email" =>$userData['email'],
                      "password"=>Hash::make($userData['password'])

            ])   ;           
       

            
            //ENVOI DE LA NOTIFICATION

            event(new Registered($newUser));  
             // $newUser->notify(new VerifyEmail($newUser));       
          
            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification.',
                'user'=>$newUser]
            , 201);
            

    }



    public function login(Request $request){

        $userData  = $request->validate([
        
            "email" => ["required","email"],
            "password" => ["required","string"],
       ]);

       $user = User::where("email",$userData["email"])->first();
       if(!$user) return response(["message"=>"No user found with that mail address $user[email]"],401);

       if(!Hash::check($userData["password"],$user->password))
         return response(["message"=>"no found password"],401);  


         $TOKEN = $user->createToken("mySecretKey")->plainTextToken;
       return  
       response([
        "user" => $user,
        "token"=> $TOKEN],200);


    }


}
