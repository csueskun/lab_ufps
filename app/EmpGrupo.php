<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpGrupo extends Model
{
    protected $table = "grupoempresa";

    public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
    public function grupo(){
        return $this->belongsTo('App\Grupo');
    }
}
