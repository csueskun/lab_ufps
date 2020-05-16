<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = "grupo";

    public function clase(){
        return $this->belongsTo('App\Clase');
    }
}
