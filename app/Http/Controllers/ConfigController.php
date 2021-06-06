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
    
    public function saveBanners(Request $request){
        $file = base_path().'/../json/banners.json';
        $fp = fopen($file, 'w');
        fwrite($fp, json_encode($request->input('banners'));
        fclose($fp);
        return response()->json(['code' => 200]);
    }

    public function upload(Request $request){
        $file = $request->file('file');
        $location = base_path().'/../img/banners/';
        $extension = $file->getClientOriginalExtension();
        $save_as = (int) round(microtime(true) * 1000).'.'.$extension;
        $file->move($location, $save_as);
        if($file){
            return response()->json(['saved' => $save_as], 200);
        }
        else{
            return response()->json(['msg' => 'No se pudo subir el archivo'], 540);
        }
    }


    //
}
