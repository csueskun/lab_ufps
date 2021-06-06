<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ConfigController extends Controller
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

    public function getBanners(){
        $file = base_path().'/../json/banners.json';
        $banners = '';
        foreach(file($file) as $line) {
            $banners .= $line;
        }
        return response()->json(['data' => $banners]);
    }


    //
}
