<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request){
        
        $this->validate($request, [
            'documento' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('documento', $request->input('documento'))->first();
        if(!$user){
            return response()->json(['msg'=>'Usuario no encontrado'], 401);
        }
        // if(Hash::check($request->input('password'), $user->password)){
        if($request->input('password')==$user->password){
            $apiToken = base64_encode($this->generateRandomString(40));
            User::where('documento', $request->input('documento'))->update(['api_token' => "$apiToken"]);
            $user->api_token = $apiToken;
            return response()->json(['user'=>$user]);
        }else{
            return response()->json(['msg'=>'Usuario o contraseña equivocados'], 401);
        }
    }

    public function logout(Request $request){
        $res = User::where('id', Auth::user()->id)->update(['api_token' => base64_encode($this->generateRandomString(40))]);
        return response()->json([]);
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    //
}
