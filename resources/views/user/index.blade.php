@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ action('\App\Http\Controllers\UsersController@create') }}"><span class="btn btn-dark">Gebruiker toevoegen</span></a>
        </div>
    </div>
    <br>
    <div style="overflow-x:auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Voornaam</th>
                    <th scope="col">Naam</th>
                    <th scope="col">Email</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Update</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->firstname}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->role}}</td>
                        <td>
                            <a href="{{ action('\App\Http\Controllers\UsersController@edit', ['id' => $user->id]) }}"><span class="btn btn-dark">Update</span></a>
                        </td>
                        <td>
                            @if($user->groupUsers->count() == 0 && $user->seasonAdmin->count() == 0 && $user->userTeamsOne->count() == 0)
                                <form action="{{ action('\App\Http\Controllers\UsersController@destroy', ['id' => $user->id]) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection