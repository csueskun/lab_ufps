<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = "empresa";

    public function ciudad(){
        return $this->belongsTo('App\Ciudad');
    }

    public function productos(){
        return $this->hasMany('App\Producto');
    }

    public function horarios(){
        return $this->hasMany('App\Horario');
    }

    public function feedback()
    {
        return $this->hasOne('App\Feedback');
    }

}
