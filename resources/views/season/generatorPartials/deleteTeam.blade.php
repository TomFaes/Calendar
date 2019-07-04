
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
                    <form action="{{ route('team.destroy', ['seasonId' => $team->season_id ,'id' => $team->id]) }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

