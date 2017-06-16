@extends('layouts.app')

{{-- vju poziva index() metod ProfilesControllera da prikaze podatke usera tj njegov profil --}}
@section('content')
   
  <div class="container">
  	<div class="col-lg-4">

  	  <div class="panel panel-default">
  	  	<div class="panel-heading">
          <p class="text-center">
            {{ $user->name }}'s Profile.
          </p>
  	  	</div>
  	  	<div class="panel-body">
          <center>
            {{-- slika tj avatar usera, u User.php imamo metod tj accessor getAvatarAttribute() koji avatar koloni pre imena samog fajla
               tj slike usera dodaje ispred http://socnetwork2.dev/storage/ tako da ne moramo to ovde da radimo --}}
            <img src="{{ $user->avatar }}" width="70px" height="70px" style="border-radius: 50%;">
          </center>
          <br>
          {{-- prikazi adresu usera tj location kolona 'profiles' tabele preko profile() metoda User.php modela --}}
          <p class="text-center">
            {{ $user->profile->location }}
          </p>
      	  {{--btn link ka ruti profile.edit tj na edit() metod ProfilesControllera, vidljiv samo ako je id trenutno ulogovanog usera jednak id usera ciji je profil prikazan--}}
          <p class="text-center">
            @if(Auth::id() == $user->id)
              <a href="{{ route('profile.edit') }}" class="btn btn-lg btn-info">Edit Your Profile</a>
            @endif
          </p>
  	  	</div>
  	  </div>	

      {{-- u ovom divu kacimo komponentu Friend.vue, takodje ovde dajemo vrednost njenom propu profile_use_id tj id usera ciji profil gledamo
          div je vidljiv samo ako smo na profilu koji nije nas tj id ulogovanog usera !== $user->id --}}
      @if(Auth::id() !== $user->id)
        <div class="panel panel-default">
          <div class="panel-body">
            <friend :profile_user_id="{{ $user->id }}"></friend>
          </div>
        </div>
      @endif

      <div class="panel panel-default">
        <div class="panel-heading">
          <p class="text-center">
            About Me
          </p>
        </div>
        <div class="panel-body">
          <p class="text-center">
            {{-- prikazi about kolonu 'profiles' tabele preko profile() metoda User.php modela --}}
            {{ $user->profile->about }}
          </p>
        </div>
      </div>  

  	</div>  {{--kraj div-a .col-lg-4--}}
  </div>  {{--kraj div-a .container--}}


@stop