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
        $banners = json_decode(file_get_contents($file), true);
        return response()->json(['data' => $banners]);
    }


    //
}
