@extends('layouts.app')

@section('content')
    <div style="width: 100%;">
        <a href="{{ route('season.index') }}"><span class="btn btn-dark">Seizoen overzicht</span></a><br>
        <form action="{{ route('generate-season.update',  ['id' => $season->id]) }}" method="POST">
            @method('PUT')
            {{csrf_field()}}
            <input type="text" name="jsonSeason" value="{{$seasonJson}}" hidden="" >
            <button type="submit" class="btn btn-dark">Save</button>
            <a href="{{ route('generate-season.edit',  ['id' => $season->id]) }}"><span class="btn btn-dark">Opnieuw genereren</span></a><br>
        </form><br><br>
        @include('season/generatorPartials/'.$season->type)
@endsection