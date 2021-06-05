<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = "horario";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
}
