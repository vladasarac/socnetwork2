<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  
  protected $fillable = ['locaton', 'about', 'user_id'];

  //povezujemo 'users' i 'profiles' tabele one-to-one relacijom
  public function user(){
  	return $this->belongsTo('App\User');
  }

}
