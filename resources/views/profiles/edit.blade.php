@extends('layouts.app')

@section('content')
{{--ovaj vju poziva edit() metod ProfilesControllera kad se u profile.blade.php klikne link EditYourProfile, edit() vadi iz 'profiles' tabe-
-le podatke trenutno ulogovanog usera i salje ih ovde u varijabli $info--}}
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">Edit Your Profile</div>

        <div class="panel-body">
        {{--forma za updateovanje profila, ide na profile.update rutu koja gadja update() metod PrfilesControllera--}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}

          <div class="form-group">
            <label for="avatar">Upload Avatar</label>
            {{--polje za unos slike tj avatara--}}
            <input type="file" name="avatar" class="form-control">    
          </div>

          <div class="form-group">
            <label for="location">Location</label>
            {{--polje za unos adrese, ako je neka adresa vec uneta u 'profiles' tabelu bice prikazana ako nije bice prazno polje--}}
            <input type="text" name="location" value="{{ $info->location }}" class="form-control" required>    
          </div>

          <div class="form-group">
            <label for="about">About me</label>
            {{--textarea za unos about podataka, ako je nesto vec uneto u 'profiles' tabelu bice prikazano ako nije bice prazno polje--}}
            <textarea name="about" id="about" class="form-control" cols="30" rows="10" required>{{ $info->about }}</textarea>    
          </div>

          {{-- submit btn --}}
          <div class="form-group">
            <p class="text-center">
              <button class="btn btn-primary btn-lg" type="submit">
                Save Your Information  
              </button>  
            </p>  
          </div>

        </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection