@extends('layouts.app')

@section('content')

    <div class="row">
                <div class="col-12">
            @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                <a href="{{ route('season.create') }}"><span class="btn btn-dark">Seizoen toevoegen</span></a><br>
            @endif
            <br>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th scope="col">Naam</th>
                <th scope="col">Periode</th>
                <th scope="col">Dag</th>
                @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                    <th scope="col">Admin</th>
                    <th scope="col">Groep</th>
                @endif
                <th scope="col">Player Actions</th>
                @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                    <th scope="col">Admin acties</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @foreach($seasons as $season)
                    <tr>
                        <td>{{$season->name}}</td>
                        <td>{{ \Carbon\Carbon::parse($season->begin)->format('d/m/Y')}} - {{ \Carbon\Carbon::parse($season->end)->format('d/m/Y')}}</td>
                        <td>{{$season->day." ".\Carbon\Carbon::parse($season->start_hour)->format('H:i')}}</td>
                        @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                            <td>{{$season->admin->firstname." ".$season->admin->name}}</td>
                            <td>{{$season->group->name}}</td>
                        @endif
                        <td>
                            @if($season->team->count() > 0)
                                <a href="{{ route('season.show',  ['id' => $season->id]) }}"><span class="btn btn-dark">Kalender</span></a>
                            @endif
                            @if(count($season->team) == 0)
                                <a href="{{ route('absence.show',  ['id' => $season->id]) }}"><span class="btn btn-dark">Afwezigheden</span></a>
                            @endif
                        </td>
                        @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                            <td>
                                @if($season->admin_id == Auth::user()->id && count($season->team) == 0)
                                    <a href="{{ route('season.edit',  ['id' => $season->id]) }}"><span class="btn btn-dark">Update</span></a>
                                    <a href="{{ route('season.show',  ['id' => $season->id]) }}"><span class="btn btn-dark">Team toevoegen</span></a>
                                    <a href="{{ route('generate-season.edit',  ['id' => $season->id]) }}"><span class="btn btn-dark">Genereer seizoen</span></a>
                                    <form action="{{ route('season.destroy',  ['id' => $season->id]) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection