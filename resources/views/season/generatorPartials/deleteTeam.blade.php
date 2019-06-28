
<div style="overflow-x:auto;">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Team</th>
                <th scope="col">player 1</th>
            </tr>
            </thead>
            <tbody>
            @foreach($season->team as $team)
            <tr>
                <td>{{ \Carbon\Carbon::parse($team->date)->format('d/m/Y')}}</td>
                <td>{{$team->team}}</td>
                <td>{{$team->player_one->firstname." ".$team->player_one->name}}</td>

                @if($season->admin_id == Auth::user()->id)
                <td>
                    <a href="{{ action('\App\Http\Controllers\TeamsController@destroy', ['id' => $team->id]) }}"><span class="btn btn-dark" onclick="return confirm('Are you sure?')">Delete</span></a>
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

