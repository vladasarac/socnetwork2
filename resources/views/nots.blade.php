@extends('layouts.app')

{{-- vju prikazuje neprocitane notifikacije nekog usera iz 'notifications' tabele, kad se klikne link koji prikazuje koliko neprocita
     nih notifikacija ima user(link je u app.blade.php layoutu tj u UnreadNots.vue komponenti koju ovaj kaci), vju poziva notifications()-
     metod HomeControllera--}}

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-heading text-center">
            Notificatons
            <span class="badge">{{ count($nots) }}</span> 
          </div>

          <div class="panel-body">        
            <ul class="list-group">
            {{-- iteriramo kroz $nots koji je poslao notifications() metod HomeControllera u kom su sve notifikacije ulogovanog usera--}}
              @foreach($nots as $not)
                <li class="list-group-item"> 
                  {{--prikazujemo ime(user koji nam je poslao notifikaciju) i message(to je sve u data koloni 'notifications' tabele)--}}
                  {{-- ja dodo link ka profilu usera koji nam je poslao notifikaciju tj poziv za prijateljstvo ili prihvatio nas --}}
                  <a href="/profile/{{str_slug($not->data['name'])}}">{{ $not->data['name'] }}</a> 
                  &nbsp; {{ $not->data['message'] }}
                  {{--vreme kreiranja notifikacije, diffForHumans() pretvara datum u npr '2 days ago'--}}
                  <span class="pull-right">{{ $not->created_at->diffForHumans() }}</span>
                </li>
              @endforeach  
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection