<?php

namespace App\Http\Controllers;

use Auth;
use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{

  //metodu preko rute /post/create stize AJAX iz Post.vue komponente kad se klikne button ispod textarea i stize userov unos u textarea
  // tj varijabla content
  public function store(Request $request){
  	//return $request->all();
  	//upisujemo red u 'posts' tabelu tj kolone content sa userovim unosom i user_id gde upisujemo id trenutno ulogovanog usera
  	return Post::create([
  	  'user_id' => Auth::id(),
  	  'content' => $request->content	
  	]);
  }


}
