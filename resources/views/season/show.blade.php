@extends('layouts.app')

@section('content')
<div style="width: 100%;">
    <a href="{{ route('season.index') }}"><span class="btn btn-dark">Seizoen overzicht</span></a><br><br>
    @if($season->team->count() > 0)
        @include('season/generatorPartials/'.$season->type)
    @endif    
    <br><hr><br>
    @if($season->admin_id == Auth::user()->id)
        @include('season/generatorPartials/addTeam')
        <hr>
        @include('season/generatorPartials/deleteTeam')
    @endif
</div>
@endsection
