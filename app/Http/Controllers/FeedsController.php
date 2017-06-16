<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Post;
use Illuminate\Http\Request;

class FeedsController extends Controller
{

  //metod poziva get_feed() metod Feed.vue komponente preko rute '/feed' da izvuce sve postove prijatelja trenutno ulogovanog usera
  public function feed(){
  	//koristeci friends() metod traita Friendable.php prvo vadimo sve prijatelje trenutno ulogovanog usera(metod vraca array)
  	$friends = Auth::user()->friends();
  	//pravimo array u koji cemo ubaciti postove prijatelja
    $feed = array();
    //iteriramo kroz array $friends u kom su prijatelji i koresteci posts() metod User.php modela iteriramo kroz postove svakog prijatelja
    //zasebno i svaki post svakog prijatelja push-ujemo u array $feed
    foreach($friends as $friend){
      foreach ($friend->posts as $post){
        array_push($feed, $post);
      }	
    }  
    //vadimo takodje i postove trenutno ulogovanog usera i njih takodje ubacujemo u $feed array
    foreach(Auth::user()->posts as $post){
      array_push($feed, $post);	
    }
    //sortiramo $feed array usort() php funkcijom u kom su postovi ulogovanog usera i njegovih prijatelja po id koloni 'posts' tabele
    usort($feed, function($p1, $p2){
      return $p1->id < $p2->id;	
    });
    return $feed;
  }	

  //---------------------------------------------------------------------------------------------------------------------------------------

}
