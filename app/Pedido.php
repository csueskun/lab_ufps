<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = "pedido";
	
    public function empresa(){
      return $this->belongsTo('App\Empresa');
    }
}
