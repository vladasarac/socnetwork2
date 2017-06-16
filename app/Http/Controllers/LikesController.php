<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Post;
use App\Like;

class LikesController extends Controller
{

  //metod za upis reda u 'likes' tabelu tj kad neko u Like.vue klikne btn Like ispod nekog posta koji prikazuje Feed.vue komponenta, kao arg-
  //-ument stize id posta
  public function like($id){
    //nalazimo post koji je lajkovan(mada mislim da je ovo suvisno posto nam je stigao id posta kao argument a samo nam on treba za post_id 
    //kolonu 'likes' tabele)
    $post = Post::find($id);
    //upisujemo red u 'likes' tabelu tjj kolone user_id(trenutno ulogovani user) i post_id(id posta ciji je id stigao kao argument)
    $like = Like::create([
      'user_id' => Auth::id(),
      'post_id' => $post->id	
    ]);
    //vracamo like koji je upisan u 'likes' tabelu a sa njim i usera koji a je napravio(to je moguce zbog eager loadinga)a taj user nam treba-
    // da bi store.js mogao da upise sta treba u likes array
    return Like::find($like->id);
  }

  //-----------------------------------------------------------------------------------------------------------------------------------------

  //metod za brisanje reda iz 'likes' tabele kad neko ko je vec lajkovao neki post klikne Unlike btn ispod nekog posta koji prikazuje Feed.vue
  //komponenta, kao argument stize id posta
  public function unlike($id){
  	//nalazimo post u 'posts' tabeli po id-u koji je stigao kao argument(mada mislim da je ovo suvisno posto nam je stigao id posta kao  
  	//argument a samo nam on treba za post_id kolonu 'likes' tabele)
  	$post = Post::find($id);
    //brisemo red u 'likes' tabeli gde je kolona user_id jednaka id-u ulogovanog usera a kolona post_id jednaka id-u posta koji je stigao kao
    //argumenat
  	$like = Like::where('user_id', Auth::id())
  		        ->where('post_id', $post->id)
  		        ->first();
  	//brisemo pronadjeni like
  	$like->delete();
  	return $like->id; //vracamo id obrisanog posta da bi komponenta mogla da ga odstrani iz DOM-a  	
  }




}
