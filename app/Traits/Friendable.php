<?php

namespace App\Traits;

use App\Friendship;

trait Friendable
{
  //kad neko nekom posalje zahtev za prijateljstvo poziva se ovaj metod koji u 'friendships' tabelu upisuje novi red tj id onoga ko -
  //-salje request i id onoga kome je request poslat, status kolona je po difoltu 0 i ostace dok ovaj drugi ne prihvati poziv
  public function add_friend($user_requested_id){
  	//if koji proverava da li je user sam sebi poslao poziv za friendship i ako jeste tj ako je id usera koji poziva metod jednak id-u -
  	//usera kom saljemo request za friendship vracamo 0
  	if($this->id === $user_requested_id){
  	  return 0;	
  	}
  	//ako neko pokusava da posalje request za friendship nekom sa kim je vec prijatelj
  	if($this->is_friends_with($user_requested_id) === 1){
	    return "already friends";
	  }
  	//ako smo ovom useru vec poslali poziv za prijateljstvo vrati  "already sent a friend request", ovde koristimo metod 
  	//has_pending_friend_request_sent_to() koji je definisan u ovom fajlu koji vraca 1 ako jesmo i 0 ako nismo
  	if($this->has_pending_friend_request_sent_to($user_requested_id) === 1){
	    return "already sent a friend request";
	  }
	  //proveravamo da li je user kom saljemo request za prijateljstvo vec poslao nama request za prijateljstvo koristeci  metod -
	  //has_pending_friend_request_from() koji je definisan u ovom fajlu i ako je to slucaj automatski prihvatamo njegov poziv preko metoda
	  //accept_friend()
	  if($this->has_pending_friend_request_from($user_requested_id) === 1){
	    return $this->accept_friend($user_requested_id);
	  }
    //upisujemo red u 'friendships' tabelu gde ce requester kolona biti id usera koji dodaje prijatelja tj ulogovanog a user_requested kolo-
    //na id usera ciji je id stigao kao argument pri pozivu metoda tj onog kome smo na profilu klikn uli btn AddFriend 
  	$friendship = Friendship::create([
  	  'requester' => $this->id, // ovo je id usera koji je pozvao ovaj metod tj onog koji salje nekom request za prijateljstvo
  	  'user_requested' => $user_requested_id // ovo je id usera kom je poslat request za prijateljstvo	
  	]);
  	//ako upise u 'friendships' tabelu vrati status 200 i $friendship objekat
  	if($friendship){
  	  return 1;	
  	}
  	//ako ne uspe
  	return 0;
  }

  //------------------------------------------------------------------------------------------------------------------------------------
  
  // metod za prihvatanje necijeg friendship requesta
  public function accept_friend($requester){
  	//proveravamo da li user koji prihvata prijateljstvo od nekog usera stvarno ima poziv od njega za prijateljstvo ako to nije slucaj tj
  	//ako metod has_pending_friend_request_from() koji je definisan u ovom fajl vrati 0 vracamo 0
  	if($this->has_pending_friend_request_from($requester) === 0){
	  return 0;
	}
  	//izvlacimo red iz 'friendships' tabele gde je requester kolona jednaka id usera koji nam je poslao poziv za prijateljstvo a kolona-
  	//-user_requester je jednaka id - u usera koji poziva ovaj metod tj prihvata prijateljstvo
  	$friendship = Friendship::where('requester', $requester)
  							  ->where('user_requested', $this->id)
  							  ->first();
    //ako postoji takav red menjamo status kolonu iz 0 u 1
  	if($friendship){
  	  $friendship->update([
  	    'status' => 1
  	  ]);	
  	  return 1;
  	}
  	//ako nismo nasli red u 'friendships' tabeli sa datim id-evima friendship requestera i user_requested-a vrati fail
  	return 0;
  }	

  //------------------------------------------------------------------------------------------------------------------------------------
  
  //metod koji vadi sve prijatelje jednog usera, tj prvo pretrazujemo 'friendships' tabelu po id usera koji poziva metod i requester-
  //-kolonu i user_requested kolonu tj i kad je on bio pozivalac na prijateljstvo i kad su njega pozivali i gde je status kolona 1 tj -
  //-poziv je prihvacen i onda sa tim id-evima pretrazujemo 'users' kolonu i vadimo podatke njegovih prijatelja
  public function friends(){
  	//array u koji ce uci svi useri kojima je user koji poziva metod poslao request za prijateljstvo i koji su ga prihvatili
  	$friends = array();
  	//iz 'friendships' vadimo redove gde je requester kolona==id usera koji poziva metod i gde je status kolona == 1
  	$f1 = Friendship::where('status', 1)
  					->where('requester', $this->id)
  					->get();
  	//iz user tabele, koristeci $f1 u koji smo ubacili usere kojima smo poslali request a oni ga prihvatili, vadimo usere po id koji je-
  	//-upisan u user_requested kolonu 'friendships' tabele i ubacujemo ih u $friends array
  	foreach($f1 as $friendship){
  	  array_push($friends, \App\User::find($friendship->user_requested));
  	}
  	//sada obrnuto prethodnom, vadimo redove iz 'friendships' tabele u kojima je user_requested kolona==id usera koji poziva ovaj metod-
  	//-i gde je status kolona ==1 tj poziv je prihvacen, tj vadimo one redove gde je nama slat request za friendship a mi prihvatili
  	$friends2 = array();
  	//
  	$f2 = Friendship::where('status', 1)
  	  				->where('user_requested', $this->id)
  	  				->get();
  	//iz user tabele, koristeci $f2 u koji smo ubacili usere koji su nam poslali request a mi ga prihvatili, vadimo usere po id koji je-
  	//-upisan u requester kolonu 'friendships' tabele i ubacujemo ih u $friends2 array
  	foreach($f2 as $friendship){
  	  array_push($friends2, \App\User::find($friendship->requester));
  	}
  	//sada od $friends i $friends2 arraya pravimo jedan array, u tom arrayu su svi prijatelji usera koji je pozvao metod, i oni kojima je
  	//-on poslao poziv za prijateljstvo a oni ga prihvatili i oni koji su njemu poslali poziv a on ih prihvatio
  	return array_merge($friends, $friends2);
  }

  //------------------------------------------------------------------------------------------------------------------------------------
  
  //metod koji iz 'users' tabele vadi sve koji su poslali request za prijateljstvo useru koji poziva metod a on ih nije jos prihvatio tj
  //-kolona status 'friendships' tabele je 0 
  public function pending_friend_requests(){
  	//
  	$users = array();
  	//vadimo sve redove 'friendships' tabele u kojoj je status kolona 0 a user_requested tj onaj kom je poslat poziv je jednaka id-u -
  	//usera koji poziva metod
  	$friendships = Friendship::where('status', 0)
					         ->where('user_requested', $this->id)
					         ->get();
  	//iz 'users' tabele vadimo podatke usera koji su poslali request useru koji je pozvao metod koristecei $friendship array tj kolonu-
    //-requester 'friendships' tabele u kojoj je id usera koji salje request za prijateljstvo i ubacujemo ih u array $users
    foreach($friendships as $friendship){
      array_push($users, \App\User::find($friendship->requester));
    }
    //
    return $users;
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod koji vadi samo id-eve(znaci ne sve podatke) prijatelja usera koji poziva ovaj metod, prvo pozivamo metod friends() koji je -
  //-definisan u ovom fajlu iznad koji vadi sve prijatelje usera koji poziva ovaj metod i onda od toga pravi kolekcju i onda iz toga -
  //-izvlaci samo id usera koji su prijatelji useru koji poziva metod
  public function friends_ids(){
  	return collect($this->friends())->pluck('id');
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod proverava da li je user sa id-em koji stigne kao parametar ovog metoda prijatelj sa userom koji poziva ovaj metod tj da li je-
  //njegov id u arrayu koji vraca friends_ids() metod koji je definisan odmah iznad
  public function is_friends_with($user_id){
  	//koristeci in_array() kom kao argumente dajemo id usera koji je stigao kao parametar i array koji vraca friends_ids() metod pretra-
  	//zujemo array koji vraca friends_ids i ako nadjemo id koji trazimo vracamo true a ako ne vracamo false 
  	if(in_array($user_id, $this->friends_ids()->toArray())){
  	  return 1;	
  	}else{
  	  return 0;		
  	}
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod koji vadi samo id-eve(znaci ne sve podatke) usera koji su poslali poziv za prijateljstvouseru koji poziva ovaj metod, prvo -
  //-pozivamo metod pending_friend_requests() koji je definisan u ovom fajlu iznad koji vadi sve usere koji su poslali poziv useru koji-
  //-poziva ovaj metod i onda od toga pravi kolekcju i onda iz toga izvlaci samo id usera koji su poslali request za friendship
  public function pending_friend_requests_ids(){
  	return collect($this->pending_friend_requests())->pluck('id')->toArray();
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod kojim vadimo sve usere kojima je user koji poziva metod poslao zahtev za prijateljstvo a oni jos uvek nisu prihvatili tj kolo-
  //-na status 'friendships' tabele je 0
  public function pending_friend_requests_sent(){
  	$users = array();
  	//vadimo redove 'friendships' tabele u kojima je requester kolona jednaka id-u usera koji poziva metod a kolona status 0
  	$friendships = Friendship::where('status', 0)
  							 ->where('requester', $this->id)
  							 ->get();
  	//koristeci $friendships array vadimo iz 'users' tabele usere kojima smo poslali poziv a nisu nam odgovorili i dalje			
  	foreach ($friendships as $friendship){
  	  array_push($users, \App\User::find($friendship->user_requested));
  	}
  	return $users;
  }

  //------------------------------------------------------------------------------------------------------------------------------------
  
  //metod koji samo vadi id-eve usera kojima je user koji poziva metod poslao request za prijateljstvo a oni i dalje nis prihvatili, ko-
  //-risti metod pending_friend_requests_sent() koji je definisan iznad a koji vraca array sa svim podatcima tih usera i iz njega vadi -
  //-samo njihove id-eve
  public function pending_friend_requests_sent_ids(){
	return collect($this->pending_friend_requests_sent())->pluck('id')->toArray();
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod koji proverava da li user koji poziva metod ima pending request od odredjenog usera ciji id stize kao argument pri pozivanju -
  //-metoda, poziva se metod pending_friend_requests_ids() koji vraca array sa id-evima usera koji su poslali request za prijateljstvo-
  //-useru koji poziva ovaj metod a on im jos uvek nije odgovorio
  public function has_pending_friend_request_from($user_id){
	if(in_array($user_id, $this->pending_friend_requests_ids())){
	  return 1;
	}else{
	  return 0;
	}
  }

  //------------------------------------------------------------------------------------------------------------------------------------

  //metod koji proverava da li je user koji poziva ovaj metod poslao request za prijateljstvo useru ciji id stize kao argument metoda -
  //-a ovaj i dalje nije odgovorio, poziva se metod pending_friend_requests_sent_ids() koji je definisan iznad koji vraca array sa id-ev-
  //-ima usera kojima je pozivalac ovog metoda poslao request za prijateljstvo a oni jos nisu odgovorili
  public function has_pending_friend_request_sent_to($user_id){
	if(in_array($user_id, $this->pending_friend_requests_sent_ids())){
	  return 1;
	}else{
	  return 0;
	}
  }

  //------------------------------------------------------------------------------------------------------------------------------------
  //samo proba
  // public function hello(){
  // 	return "hello friend";
  // }

}