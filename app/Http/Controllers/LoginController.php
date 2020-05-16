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
            'usuario' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('usuario', $request->input('usuario'))->first();
        if(Hash::check($request->input('password'), $user->password)){
            $apiToken = base64_encode($this->generateRandomString(40));
            User::where('usuario', $request->input('usuario'))->update(['api_token' => "$apiToken"]);
            return response()->json(['api_token' => $apiToken]);
        }else{
            return response()->json([], 401);
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
