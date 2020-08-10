@extends('layouts.app')

@section('content')
    @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
        <div class="row">
            <div class="col-12">
                <a href="{{ route('group.create') }}"><span class="btn btn-dark">Group toevoegen</span></a>
            </div>
        </div><br>
    @endif
    <div style="overflow-x:auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Naam</th>
                    <th scope="col">Admin</th>
                    <th scope="col">Update</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{$group->id}}</td>
                        <td>{{$group->name}}</td>
                        <td>{{$group->admin->firstname." ".$group->admin->name}}</td>
                        <td>
                            @if($group->admin_id == Auth::user()->id)
                                <a href="{{ route('group.edit',  ['id' => $group->id]) }}"><span class="btn btn-dark">Update</span></a>
                                <a href="{{ route('group.user.index',  ['id' => $group->id]) }}"><span class="btn btn-dark">Group users</span></a>
                            @endif
                        </td>
                        <td>
                            @if($group->admin_id == Auth::user()->id && $group->users->count() == 0 && $group->seasonGroup->count() == 0)
                                <form action="{{ route('group.destroy',  ['id' => $group->id]) }}" method="POST">                             
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete Group</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection