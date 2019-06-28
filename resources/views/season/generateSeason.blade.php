@extends('layouts.app')

@section('content')


    <div style="width: 100%;">
        <a href="{{ action('\App\Http\Controllers\SeasonsController@index')}}"><span class="btn btn-dark">Seizoen overzicht</span></a><br><br>
        <form method="post" action="{{ action('\App\Http\Controllers\SeasonsController@saveSeason', ['id' => $season->id])}}">
            {{csrf_field()}}
            <input type="text" name="jsonSeason" value="{{$seasonJson}}" hidden="" >
            <button type="submit" class="btn btn-dark">Save</button>
            <a href="{{ action('\App\Http\Controllers\SeasonsController@generateSeason', ['id' => $season->id]) }}"><span class="btn btn-dark">Opnieuw genereren</span></a>
        </form><br><br>
        
        @include('season/generatorPartials/'.$season->type)
        
@endsection