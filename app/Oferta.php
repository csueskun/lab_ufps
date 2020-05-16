<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = "oferta";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
}
