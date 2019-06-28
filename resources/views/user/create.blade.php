@extends('layouts.app')

@section('content')

<div style="width: 100%;">
    <a href="{{ action('\App\Http\Controllers\UsersController@index') }}"><span class="btn btn-dark">Gebruikers overzicht</span></a><br><br>
    <form method="post" action="{{ action('\App\Http\Controllers\UsersController@store')}}">
    {{csrf_field()}}
    
        <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="name" class="col-4 col-lg-1 control-label">Voornaam</label>
                <div class="col-lg-4 col-8">
                    <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" autofocus>
                    @if ($errors->has('firstname'))
                        <span class="help-block">
                            <strong>{{ $errors->first('firstname') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="name" class="col-4 col-lg-1 control-label">Achternaam</label>
                <div class="col-lg-4 col-8">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="email" class="col-4 col-lg-1 control-label">E-Mail adres</label>
                <div class="col-lg-4 col-8">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="password" class="col-4 col-lg-1 control-label">Wachtwoord</label>
                <div class="col-lg-4 col-8">
                    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-0 col-lg-2"></div>
                <label for="password-confirm" class="col-4 col-lg-1 control-label">Bevestig wachtwoord</label>
                <div class="col-lg-4 col-8">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}">
                </div>
            </div>
        </div>
    
        <div class="form-group">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <button type="submit" class="btn btn-dark">
                        Creëer nieuwe gebruiker
                    </button>
                </div>
            </div>
        </div>
    </form>
    
</div>

<hr>
@endsection

