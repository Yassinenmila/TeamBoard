<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login (Request $request){

        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user|| !Hash::check($request->password,$user->password)){
            throw ValidationException::withMessages([ 'email'=>['Les informations sont incorrectes'] ]);
        }

        $token= $user->createToken('api_Token')->plainTextToken;

        response()->json([
            'user'=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
            ],
            'Token'=>$token,
            'message'=>'connection reussie'
        ],200);

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=> 'deconnection reussie'
        ],200);
    }
}
