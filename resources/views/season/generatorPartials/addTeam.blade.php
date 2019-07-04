
<form method="post" action="{{ route('team.store',  ['seasonId' => $season->id]) }}">

    {{csrf_field()}}
    <input id="seasonId" type="text" class="form-control" name="seasonId" value="{{ $season->id }}" hidden>
    <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
        <div class="row">
            <div class="col-0 col-lg-2"></div>
            <label for="date" class="col-4 col-lg-1 control-label">Date</label>
            <div class="col-lg-4 col-8">
                <select class="form-control" name="date" id="date">
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

    <div class="form-group{{ $errors->has('team') ? ' has-error' : '' }}">
        <div class="row">
            <div class="col-0 col-lg-2"></div>
            <label for="team" class="col-4 col-lg-1 control-label">Team</label>
            <div class="col-lg-4 col-8">
                <select class="form-control" name="team" id="team">
                    <option value="{{ old('team') }}"></option>
                    <option value="team1">Team 1</option>
                    <option value="team2">Team 2</option>
                    <option value="team3">Team 3</option>
                </select>
                @if ($errors->has('team'))
                    <span class="help-block">
                        <strong>{{ $errors->first('team') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group{{ $errors->has('playerOneId') ? ' has-error' : '' }}">
        <div class="row">
            <div class="col-0 col-lg-2"></div>
            <label for="playerOneId" class="col-4 col-lg-1 control-label">Speler 1</label>
            <div class="col-lg-4 col-8">
                <select class="form-control" name="playerOneId" id="playerOneId">
                    <option value=""></option>
                    @foreach($season->group->users as $user)
                    <option value="{{$user->id}}">{{ $user->firstname." ".$user->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('date'))
                <span class="help-block">
                            <strong>{{ $errors->first('playerOneId') }}</strong>
                        </span>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-4">
                <button type="submit" class="btn btn-dark">
                    Team toevoegen
                </button>
            </div>
        </div>
    </div>
</form>