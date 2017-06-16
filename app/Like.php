<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
  //ovde upisujemo user da bi kad god izvlacimo like iz 'likes' tabele iz 'users' tabele izvukli podatke usera koji je dodao lajk posto nam-
  //-trebaju ti podatci u Feed.vue komponenti da bi pokazali koji je user lajkovao koji post
  public $with = ['user'];

  //kolone za popunjavanje
  protected $fillable = ['user_id','post_id'];

  //one-to-many relacija izmedju 'likes' i 'posts' tabela posto post moze imati vise lajkova a lajk moze pripadati samo jednom postu
  public function post(){
  	return $this->belongsTo('App\Post');
  }

  //one-to-many relacija sa 'users' tabelom posto user pravi lajkove i moze ih imati vise a jedan lajk pripada samo jednom useru
  public function user(){
  	return $this->belongsTo('App\User');
  }
  

}
