@extends('layouts.app')

@section('content')

<div style="width: 100%;">
    <a href="{{ action('\App\Http\Controllers\GroupsController@index') }}"><span class="btn btn-dark">Groep overzicht</span></a><br><br>
    <form method="POST" action="{{ action('\App\Http\Controllers\GroupsController@update', ['id' => $group->id])}}">
        {{csrf_field()}}
        {{ method_field('PUT')}}
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="name" class="col-4 col-lg-1 control-label">Name</label>
                <div class="col-lg-4 col-8">
                    <input id="name" type="text" class="form-control" name="name" value="{{ $group->name }}" autofocus>
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('userId') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="userId" class="col-4 col-lg-1 control-label">Admin</label>
                <div class="col-lg-4 col-8">
                    <select class="form-control" name="userId" id="userId">
                        <option value=""></option>
                        @foreach($users AS $user)
                            @if($user->id == $group->admin_id)
                                <option value="{{$user->id}}" selected="true" >{{$user->firstname." ".$user->name}}</option>
                            @else
                                <option value="{{$user->id}}">{{$user->firstname." ".$user->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('userId'))
                        <span class="help-block">
                            <strong>{{ $errors->first('userId') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <button type="submit" class="btn btn-dark">
                        Update
                    </button>
                </div>
            </div>
        </div> 
    </form>
    
@endsection

