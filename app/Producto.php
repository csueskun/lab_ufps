<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = "producto";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
	
	  public function tipoproducto(){
        return $this->belongsTo('App\Tipoproducto');
    }
	
	  public function tipocategoria(){
        return $this->belongsTo('App\Tipocategoria');
    }
}
