@extends('layouts.app')

@section('content')
    
    <div style="width: 100%;">
        <a href="{{ route('season.index') }}"><span class="btn btn-dark">Seizoen overzicht</span></a><br><br>

        <form method="post" action="{{ route('season.update', ['id' => $season->id])}}">
            {{csrf_field()}}
            {{ method_field('PUT')}}
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="name" class="col-4 col-lg-1 control-label">Naam</label>
                    <div class="col-lg-4 col-8">
                        <input id="name" type="text" class="form-control" name="name" value="{{ $season->name }}" autofocus>
                        @if ($errors->has('name'))
                            <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group{{ $errors->has('begin') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="name" class="col-4 col-lg-1 control-label">Begin datum</label>
                    <div class="col-lg-4 col-8">
                        <input id="begin" type="date" class="form-control" name="begin" value="{{ $season->begin }}" autofocus>
                        @if ($errors->has('begin'))
                            <span class="help-block">
                            <strong>{{ $errors->first('begin') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="name" class="col-4 col-lg-1 control-label">Eind datum</label>
                    <div class="col-lg-4 col-8">
                        <input id="end" type="date" class="form-control" name="end" value="{{ $season->end }}" autofocus>
                        @if ($errors->has('end'))
                            <span class="help-block">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group{{ $errors->has('hour') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="name" class="col-4 col-lg-1 control-label">Begin Uur</label>
                    <div class="col-lg-4 col-8">
                        <input id="hour" type="time" class="form-control" name="hour" value="{{ $season->start_hour }}" autofocus>
                        @if ($errors->has('hour'))
                            <span class="help-block">
                            <strong>{{ $errors->first('hour') }}</strong>
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
                                @if($user->id == $season->admin_id)
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

            <div class="form-group{{ $errors->has('groupId') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="groupId" class="col-4 col-lg-1 control-label">Groep</label>
                    <div class="col-lg-4 col-8">
                        <select class="form-control" name="groupId" id="groupId">
                            <option value=""></option>
                            @foreach($groups AS $group)
                                @if($group->id == $season->group_id)
                                    <option value="{{$group->id}}" selected="true" >{{$group->name}}</option>
                                @else
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        @if ($errors->has('groupId'))
                            <span class="help-block">
                            <strong>{{ $errors->first('groupId') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                <div class="row">
                    <div class="col-0 col-lg-2"></div>
                    <label for="type" class="col-4 col-lg-1 control-label">Type</label>
                    <div class="col-lg-4 col-8">
                        <select class="form-control" name="type" id="type">
                            <option value=""></option>

                            <option value="GenerateThursdaySeason" <?php echo $season->type == "GenerateThursdaySeason" ? "selected=true" : "" ?> >Donderdag seizoen</option>
                        </select>
                        @if ($errors->has('type'))
                            <span class="help-block">
                            <strong>{{ $errors->first('type') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-dark">
                            Update season
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
