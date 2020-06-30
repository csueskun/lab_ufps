<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_Servicio extends Model
{
    protected $table = "detalle_servicio";

    public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
    public function servicio(){
        return $this->belongsTo('App\Servicio');
    }
}
