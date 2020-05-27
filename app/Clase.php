<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table = "clase";

    public function grupos(){
        return $this->hasMany('App\Grupo');
    }
}
