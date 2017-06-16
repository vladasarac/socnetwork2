<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//proba za trait Friendable.php
// Route::get('/hello', function(){
//   return Auth::user()->hello();	
// });
//ruta za testiranje add_friend() metoda trait-a Friendable.php, metod trazi parametar tj id usera kom saljemo poziv, takodje vadimo i -
//-prvi id tj prvog usera koji navodno salje poziv za prijateljstvo
Route::get('/add', function () {
    //return \App\User::first()->add_friend(4);
    return \App\User::find(4)->add_friend(2);
});
//ruta za testiranje accept_friend() metoda traita Friendable.php, sada user kom je poslat zahtev za prijateljstvo prihvata to i -
//-menja se status kolona 'friendships' tabele iz 0 u 1
Route::get('/accept', function () {
    return \App\User::find(2)->accept_friend(4);
});
//ruta za testiranje friends() metoda traita Friendable.php koji vadi sve prijatelje usera koji poziva metod u ovom slucaju sa id==1
// Route::get('/friends', function () {
//     return \App\User::find(2)->friends();
// });
//ruta za testiranje pending_friend_requests() metoda traita Friendable.php koji vadi usere koji su pozivaoc metoda poslali zahtev za pri-
//-jateljstvo a on i dalje nije odgovorio tj kolona status u 'friendships' tabeli je 0
// Route::get('/pending', function () {
//     return \App\User::find(5)->pending_friend_requests();
// });
//ruta za testiranje friends_ids() metoda traita Friendable.php koji vadi samo id-eve prijatelja usera koji je pozvao metod, koriseci pri-
//- tom friends() metod koji je takodje definisan u trait Friendable ali to sad nije vazno...
// Route::get('/friendsids', function () {
//     return \App\User::find(1)->friends_ids();
// });
//ruta za testiranje metoda is_friends_with() traita Friendable.php koji proverava da li je user ciji id damo kao parametar metodu prijat-
//-elj sa userom koji poziva ovaj metod
// Route::get('/isfriend', function () {
//     return \App\User::find(3)->is_friends_with(5);
// });
Route::get('/ch', function () {
    return \App\User::find(4)->has_pending_friend_request_sent_to(5);
});

//ruta za test kojoj Friend.vue salje id usera cije podatke ova ruta vadi iz 'users' tabele i salje ih nazad u Friend.vue komponentu
// Route::get('/check_relationship_stauts/{id}', function ($id) {
//     return \App\User::find($id);
// });



Auth::routes();

Route::get('/home', 'HomeController@index');


//grupa auth middleware
Route::group(['middleware' => 'auth'], function(){
  
  //ruta ide na index() metod ProfilesControllera
  Route::get('/profile/{slug}', [
    'uses' => 'ProfilesController@index',
    'as' => 'profile'	
  ]);
  
  //ruta ide na edit() metod ProfilesControllera koji vraca vju edit.blade.php u kom je forma za editovanje profila i salje mu podatke usera iz 'profiles' tabele preko profile() metoda User.php modela
  Route::get('/profile/edit/profile', [
    'uses' => 'ProfilesController@edit',
    'as' => 'profile.edit'	
  ]);

  //kad se sabmituje forma u edit.blade.php iz 'socnetwork/resources/views/profiles' za popunjavanje 'profiles' tabele podatcima o useru
  //ide na update() metod ProfilesControllera
  Route::post('/profile/update/profile', [
    'uses' => 'ProfilesController@update',
    'as' => 'profile.update'	
  ]);

  //ruta koja salje AJAX iz Friend.vue check() metodu FriendshipsControllera koji proverava kakav je status izmedju ulogovanog usera i usra-
  //ciji on profil gleda tj ciji id stigne AJAX-om 
  Route::get('/check_relationship_stauts/{id}', [
    'uses' => 'FriendshipsController@check',
    'as' => 'check'
  ]);

  //ruta koja ide na metod ad_friend() FriendShipsControllera koji poziva add_friend() metod traita friendable.php da upise u 'friendships'
  //tabelu ko je kome proslao request za frendship, stize AJAX iz Frien.vue komponente kad se klikne btn AddFriend na necijem profilu
  Route::get('/add_friend/{id}', [
    'uses' => 'FriendshipsController@add_friend',
    'as' => 'add_friend'
  ]);

  //ruta koja gadja accept_friend() metod FriendshipsControllera koji ce pozvati metod accept_friend() traita Friendable.php kad neko klikn
  //ne AcceptFriend btn na profilu nekog ko mu je poslao friend request, stize AJAX iz Friend.vue komponente
  Route::get('/accept_friend/{id}', [
    'uses' => 'FriendshipsController@accept_friend',
    'as' => 'accept_friend'
  ]);

  //ruta za sada sama vadi iz 'notifications' tabele notifikacije za usera koji je trenutno ulogovan a koje su neprocitane koristeci
  //laravelov metod unreadNotifications
  Route::get('get_unread', function(){
  	return Auth::user()->unreadNotifications;
  });

  //metod gadja notifications() iz HomeController.php koji vadi sve notifikacije nekog usera i prikazuje ih u nots.blade.php
  Route::get('/notifications', [
    'uses' => 'HomeController@notifications',
    'as' => 'notifications'
  ]);

  //ruta ide na store() metod PostsControllera, stize AJAX iz Post.vue komponente kad se klikne btn i onda klik poziva post_create() metod
  //koji salje AJAX preko ove rute sa content-om tj userovim unosom u <textarea> 
  Route::post('/create/post', [
    'uses' => 'PostsController@store'
  ]);

  //rutu poziva komponenta Feed.vue da preko feed() metoda FeedsControllera izvadi sve postove prijatelja trenutno ulogovanog usera i 
  //prikaze ih posto mu ih metod vrati
  Route::get('/feed', [
    'uses' => 'FeedsController@feed',
    'as' => 'feed'
  ]);

  //rutu poziva get_auth_user_data() metod Init.vue komponente da mu posalje poatke trenutno logovanog usera
  Route::get('/get_auth_user_data', function(){
    return Auth::user();
  });

  //ruta ka like() metodu LikesControllera kad se klikne Like btn koji pravi Like.vue komponenta ispod nekog posta koji prikazuje Feed.vue
  //metod zatim upisuje red u 'likes' tabelu
  Route::get('/like/{id}', [
    'uses' => 'LikesController@like'
  ]);

  //ruta ka unlike() metodu LikesControllera kad se klikne Unlike btn koji pravi Like.vue komponenta ispod nekog posta koji prikazuje 
  //Feed.vue, metod zatim brise red u 'likes' tabeli
  Route::get('/unlike/{id}', [
    'uses' => 'LikesController@unlike'
  ]);

  //rutu poziva  metod search() Search.vue komponente kad algolia vrati usere posle dinamic searcha da bi znali da li da stavimo AddFriend-
  //btn kad prikazujemo usera u pretrazi, ja dodo
  Route::get('/friendsids', function () {
    return \App\User::find(Auth::id())->friends_ids();
  });

  //rutu poziva  metod search() Search.vue komponente kad algolia vrati usere posle dinamic searcha da bi znali da li da stavimo AceptFriend-
  //btn kad prikazujemo usera u pretrazi, ja dodo
  Route::get('/pendingfriendsrequestsids', function () {
    return \App\User::find(Auth::id())->pending_friend_requests_ids();
  });

  //rutu poziva  metod search() Search.vue komponente kad algolia vrati usere posle dinamic searcha da bi znali da li da stavimo AceptFriend-
  //btn kad prikazujemo usera u pretrazi, ja dodo
  Route::get('/pendingfriendrequestssentids', function () {
    return \App\User::find(Auth::id())->pending_friend_requests_sent_ids();
  });

});