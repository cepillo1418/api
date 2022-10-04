<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    //methode d'inscription
    public function InscrisUtilisateurs(Request $request){
        $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'password' => 'required|string|min:8|confirmed',
             
        ]);

        $utilisateur = new User; 

  
        $utilisateur -> name = $request->name;
        $utilisateur-> email = $request->email; 
        $utilisateur-> password = Hash::make($request->password);
        $utilisateur-> password_confirmation = Hash::make($request->password_confirmation);

        $utilisateur->save(); 
        dd($utilisateur);

        return response()->json([
            'msg' =>'Utilisateur crée avec succés', 
            'status_code' => 200 , 
            'utilisateur' => $utilisateur
        ]);
    }

    //methode d'authentification

    public function connexion(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)){
                return response()->json([
                    'status_code' => 500,
                    'message' => 'non authoriser'
                ]);
            }
            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'acces_token' => $tokenResult, 
                'token_type' => 'Bearer', 
            ]);
        } catch (Exception $error){
            return response()->json([
                'status_code' => 500,
                'message' => 'Error in login', 
                'error' => $error,
            ]);
        }
    }
}
