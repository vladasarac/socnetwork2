<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{

  //metod poziva ruta 'check' kada stigne AJAX iz Friend.vue komponente koji salje id usera kom zelimo da proverimo u kakvoj je relaciji
  //sa nama pa onda pozivamo metode traita Friendable.php ciji metodi to proveravaju i u zavisnosti od toga saljemo odgovor u Friend.vue
  public function check($id){
    //pozivamo is_friends_with() metod traita Friendable.php koji vraca 1 ako je user koji poziva metod friend sa userom ciji id posaljemo
    //-kao parametar metodu, ako vrati 1 tj ako su prijatelji tj stats kollona 'friendships' tabele je 1 vrati status => friends
    if(Auth::user()->is_friends_with($id) === 1){
      return ['status' => 'friends'];	
    }
    //pozivamo has_pending_friend_request_from() metod traita Friendable.php koji vraca 1 ako user koji poziva ovaj metod ima poslat req-
    //uest za friendship od usera ciji id saljemo kao argument tj ovaj nas je pozvao za prijatelja ali nismo jos uvek prihvatili
    if(Auth::user()->has_pending_friend_request_from($id)){
      return ['status' => 'pending'];	
    }
    //pozivamo has_pending_friend_request_sent_to() metod traita Friendable.php koji vraca 1 ako je user koji poziva metod poslao useru -
    //ciji id salje kao argument zahtev za prijateljstvo a ovaj mu jos nije odgovorio
    if(Auth::user()->has_pending_friend_request_sent_to($id)){
      return ['status' => 'waiting'];	
    }
    //ako u 'friendships' tabeli nema reda u kom su oba id -a i usera koji poziva metod i usera ciji id je stigao AJAX-om znaci da medju -
    //njima nema nikakve relacije i vracamo status 0
    return ['status' => 0];
  }

  //----------------------------------------------------------------------------------------------------------------------------------
  
  //Metod kojim upisujemo red u 'friendships' tabelu kad se klinke btn AddFriend na necijem prfilu iz Friend.vue komponente stize AJAX preko-
  //rute add_friend/id i onda ovde pozivamo metod add_friend()(saljemo mu id usera kog Add-ujemo) iz traita Friendable.php koji ce odraditi ceo poso
  public function add_friend($id){
  	$resp = Auth::user()->add_friend($id);
  	//pozivamo NewFriendRequest notification klasu da posalje notifikaciju useru kog smo addovali za prijaelja, tj poslace mu mail i takodje 
  	// ce pusher.com poslati notifikaciju tako da ce mu se pojaviit alert na ekranu, kao argument ubacujemo usera koji ga je dodao tj usera 
  	//koji je ulogovan
  	User::find($id)->notify(new \App\Notifications\NewFriendRequest(Auth::user()));
  	//vracamo $resp u Friend.vue
  	return $resp;
  }

  //----------------------------------------------------------------------------------------------------------------------------------

  //metod kojim updateujemo status kolonu 'friendships' tabele tj kojim neko prihvata neciji poziv za friendship, stize AJAX iz Friend.vue-
  //komponente preko rute add_friend/id sa id em usera kog prihvatamo i onda pozivamo accept_friend() metod traita Friendable.php koji radi
  //ceo poso
  public function accept_friend($id){
  	//pozivamo accept_friend() metod traita Friendable.php da updateuje 'friendships' tabelu tj kolonu status
  	$resp = Auth::user()->accept_friend($id);
  	//nalazimo usera kojem je prihvacen poziv za prijateljstvo i pozivamo na njemu FriendRequestAccepted notification klasu da mu posalje -
  	//mail i notifikaciju preko pusher.com a kao argument dajemo usera koji je trenutno ulogovan tj onog koji ga je prihvatio za frienda	
  	User::find($id)->notify(new \App\Notifications\FriendRequestAccepted(Auth::user()));									   
  	//vracamo $resp u Friend.vue($resp ce biti 1)
  	return $resp;
  }

  //----------------------------------------------------------------------------------------------------------------------------------

  //ovde ce biti metod kojim cemo pretrazivati 'users' tabelu kad u Search.vue user unese nesto u input pa klikne ShowMoreResults btn-
  //jer sam namestio da algolia.com vraca samo 4 rezultata

}
