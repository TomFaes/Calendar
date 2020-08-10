@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-12"></div>
            <div class="col-lg-6 col-12">
                    @if(isset($seasonsdata) === true)
                        @foreach($seasonsdata as $seasonId=>$data)
                            <h3>
                                {{ $data['season']->name }}
                                <?php echo CalendarLink::generateLink($data['season']->name, \Carbon\Carbon::parse($data['date']." ".$data['season']->start_hour) , \Carbon\Carbon::parse($data['date']." ".$data['season']->start_hour)->addHour(2)) ?>
                            </h3>
                            <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Player</th>
                                        <th scope="col" style="text-align: center">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $data['date'])->format('d/M') }}</th>
                                        <tbody>
                                    
                                    </tbody>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['users'] as $user)
                                            <tr>
                                                <td>{{ $user['firstname'] }}</td>

                                                @if(isset($data['display'][$user['id']]) === true)
                                                    <td style="text-align: center">{{ $data['display'][$user['id']] }}</td>
                                                @elseif(isset($data['absenceDays'][$user['id']][$data['date']]) === true)
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

