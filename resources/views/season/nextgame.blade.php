@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-12"></div>
            <div class="col-lg-6 col-12">
                @if(isset($playData['season']) === true)
                    @foreach($playData['season'] as $season)
                        <h3>
                            {{ $season['name']}}
                            <?php echo CalendarLink::generateLink($season['name'], \Carbon\Carbon::parse($playData['date'][$season['id']]." ".$season['start_hour']) , \Carbon\Carbon::parse($playData['date'][$season['id']]." ".$season['start_hour'])->addHour(2)) ?>
                        </h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Player</th> 
                                <?php
                                $dateView = date_create($playData['date'][$season['id']]);
                                ?>
                                <th scope="col" style="text-align: center">{{date_format($dateView, "d/M")}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($playData['users'][$season['id']] as $user)
                                <tr>
                                    <td>{{ $user['firstname'] }}</td>
                                    @if(isset($playData['dayplayers'][$season['id']][$user['id']]) === true)
                                        <td style="text-align: center">{{ $playData['dayplayers'][$season['id']][$user['id']] }}</td>
                                    @elseif(isset($playData['dayabsence'][$season['id']][$user['id']][$playData['date'][$season['id']]]) === true)
                                        <td  style="background-color: red"></td>
                                    @else
                                        <td class="btn-dark"></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @else
                    Er is geen seizoen waarin je aan deelneemt
                @endif
            </div>
        </div>
    </div>
@endsection 
