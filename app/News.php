<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = "news";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
}
