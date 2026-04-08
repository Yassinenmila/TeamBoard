<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login (Request $request){

        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user|| !Hash::check($request->password,$user->password)){
            return response()->json([
                'message'=>'email ou mot de passe incorrect'
            ],401);
        }

        $token= $user->createToken('api_Token')->plainTextToken;

        return response()->json([
            'user'=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'role'=>$user->role,
            ],
            'token'=>$token,
            'message'=>'connection reussie'
        ],200);

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=> 'deconnection reussie'
        ],200);
    }

    public function me(Request $request){
        return response()->json($request->user());
    }
}
