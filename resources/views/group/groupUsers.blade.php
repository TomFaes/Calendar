@extends('layouts.app')

@section('content')

<div style="width: 100%;">
    @if($group->admin_id == Auth::user()->id)
        <a href="{{ action('\App\Http\Controllers\GroupsController@index') }}"><span class="btn btn-dark">Groep overzicht</span></a><br><br>
        <form method="post" action="{{ action('\App\Http\Controllers\GroupsController@addUsers', ['id' => $group->id])}}">
            {{csrf_field()}}
            <div class="form-group{{ $errors->has('groupUsers') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="groupUsers" class="col-4 col-lg-1 control-label">Users</label>
                    <div class="col-lg-4 col-8">
                        <select class="form-control" name="groupUsers[]" id="groupUsers" multiple="multiple" size="8">
                            <option value=""></option>

                            @foreach($nonGroupUsers as $user)
                                <option value="{{$user->id}}">{{$user->firstname." ".$user->name}}</option> 
                            @endforeach
                        </select>
                        @if ($errors->has('groupUsers'))
                            <span class="help-block">
                                <strong>{{ $errors->first('groupUsers') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <div class="col-lg-4 col-8">
                        <button type="submit" class="btn btn-dark">
                            Add users
                        </button>
                    </div>
                </div>
            </div>  
        </form>
        <hr>
    @endif
    
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Voornaam</th>
            <th scope="col">Naam</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($group->users as $user)
            <tr>
                <td>{{$user->firstname}}</td>
                <td>{{$user->name}}</td>
                <td>
                    <form method="post" action="{{ action('\App\Http\Controllers\GroupsController@deleteGroupUser', ['group_id' => $group->id])}}">
                        {{csrf_field()}}
                        <input id="userId" type="hidden" class="form-control" name="userId" value="{{ $user->id }}">
                        @if($group->admin_id == Auth::user()->id)
                            <button type="submit" class="btn btn-dark">
                                Remove user
                            </button>
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
