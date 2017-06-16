<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

  //u ovom public property-u upisujemo user i time govorimo aplikaciji da kad god izvlaci postove da sa svakim izvuce podatke usera koji ih -
  // je kreirao iz 'users' tabele, takodje izvlacicemo i likeove posta iz 'likes' tabele
  public $with = ['user', 'likes'];

  //fillable array za 'posts' tabelu
  protected $fillable = ['content', 'user_id'];	
  
  //one-to-many relacija sa 'users' tabelom posto jedan user moze imati vise postova
  public function user(){
  	return $this->belongsTo('App\User');
  }

  //relacija sa 'likes' tabelom
  public function likes(){
  	return $this->hasMany('App\Like');
  }


}
