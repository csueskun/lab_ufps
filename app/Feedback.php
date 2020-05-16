<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = "feedback";
	
	  public function empresa(){
        return $this->belongsTo('App\Empresa');
    }
}
