@extends('layouts.app')

@section('content')
    <div style="width: 100%;">
        <a href="{{ action('\App\Http\Controllers\SeasonsController@index')}}"><span class="btn btn-dark">Seizoen overzicht</span></a><br><br>
        <form method="post" action="{{ action('\App\Http\Controllers\AbsencesController@update', ['id' => $season->id])}}">
            {{csrf_field()}}
            {{ method_field('PUT')}}
            
            <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="date" class="col-4 col-lg-1 control-label">Afwezig op</label>
                    <div class="col-lg-2 col-6">
                        <select class="form-control" name="date[]" id="date" multiple="multiple" size="10">
                            <option value=""></option>
                            @foreach($days as $day)
                                <option value="{{$day}}">{{ \Carbon\Carbon::parse($day)->format('d/m/Y')}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('date'))
                            <span class="help-block">
                            <strong>{{ $errors->first('date') }}</strong>
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
                            Afwezigheid toevoegen
                        </button>
                    </div>
                </div>
            </div>
        </form>
            
        <hr>
        
        <div class="row">
            <div class="col-4 col-lg-3"></div>
            <div class="col-4 col-lg-4">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">datum</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($absences as $absence)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y')}}</td>
                            <td>
                                <form action="{{ action('\App\Http\Controllers\AbsencesController@destroy', ['id' => $absence->id]) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-dark">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
