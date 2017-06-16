<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilesController extends Controller
{

  //metod vraca vju profiles.blade.php iz 'socnetwork/resources/views/profiles'
  public function index($slug){
  	//izvuci usera iz 'users' tabele po slugu koji je stigao 
  	$user = User::where('slug', $slug)->first();
  	return view('profiles.profile')->withUser($user);
  }

  //----------------------------------------------------------------------------------------------------------------------------------

  //metod se poziva kad se u profile.blade.php klikne link Edit Your Profile koji preko rute ide na ovaj metod i iz btabele 'profiles' -
  //-vadi podatke trenutno ulogovanog usera i salje ih u vju edit.blade.php 
  public function edit(){
  	return view('profiles.edit')->with('info', Auth::user()->profile);
  }

  //----------------------------------------------------------------------------------------------------------------------------------

  //metod se poziva kad se sabmituje forma u edit.blade.php iz'socnetwork/resources/views/profiles' za popunjavanje 'profiles' tabele -
  //-podatcima o useru, dolazi preko post rute 'profile.update'
  public function update(Request $request){
    //dd($request->all());
    //validacija
    $this->validate($request, [
      'location' => 'required',
      'about' => 'required|max:255'	
    ]);
    //preko Auth::user tj usera koji je ulogovan idemo na metod profile() User.php modela i updateujemo 'profiles' tabelu gde je user_id kolo-
    //-na jednaka id-u trenutno ulogovanog usera
    Auth::user()->profile()->update([
      'location' => $request->location,
      'about' => $request->about	
    ]);
    //ako je user u formi u edit.blade.php uploadovao sliku
    if($request->hasFile('avatar')){

      //ja dodo da prvo brise staru sliku, prvo proveravamo da li je avatar kolona male.png ili female.png, jer ako jeste znaci da user jos nije-
      //-uploadovao svoju sliku i onda brisemo staru sliku
      $user = Auth::user();
      if($user->avatar != 'public/defaults/avatars/female.png' && $user->avatar != 'public/defaults/avatars/male.png'){
        Storage::delete($user->avatar);
      }
      //dovde
      
    //u kolonu avatar 'users' tabele upisacemo ono sto vrati Laravelov store() metod koji ce upisati sliku u folder 'socnetwork/storage/app/public/avatars'
    //a ako sam dobro shvatio metod ce vratiti putanju do fajla sa imenom fajla na kraju
      Auth::user()->update([
        'avatar' => $request->avatar->store('public/avatars')
      ]);
    }
    //dd(Auth::user()->profile);
    Session::flash('success', 'Profile Updated.');
    return redirect()->back();
  }

  //----------------------------------------------------------------------------------------------------------------------------------


}
