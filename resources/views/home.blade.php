@extends('layouts.app')

@section('content')
{{--ovde kacimo komponentu Post.vue iz 'socnetwork2/resources/assets/js/components' u kojoj je forma za kreiranje userovog posta--}}
<post></post>

{{--ovde kacimo komponentu Feed.vue iz 'socnetwork2/resources/assets/js/components' --}}
<feed></feed>

{{-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection
