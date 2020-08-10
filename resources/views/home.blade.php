@extends('layouts.app')

@section('content')
    <br>
    <div class="row">
            @auth
                <index-page :auth="true"></index-page>
            @else
                <index-page :auth="false"></index-page>
            @endauth
    </div>
@endsection
