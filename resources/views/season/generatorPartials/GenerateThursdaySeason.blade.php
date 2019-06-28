
<div style="overflow-x:auto; margin-left:4em;">
    <table class="table" >
        <thead>
        <tr>
            <th scope="col" style="position:absolute; width:3em; left:0;">Player</th>
            @foreach($days as $day)
                <?php
                $dateView = date_create($day);
                ?>
                <th scope="col" style="text-align: center;">{{date_format($dateView, "d/M")}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($seasonUsers as $user)
        <tr>
            <td style="position:absolute; width:3em; left:0;">{{$user->firstname}}</td>
            @foreach($days as $day)
                @if(isset($seasonArray['datum'][$day][$user->id]) === true)
                    <td style="text-align: center">{{substr($seasonArray['datum'][$day][$user->id], -1)}}</td>
                @elseif(isset($seasonAbsences[$user->id][$day]) === true)
                    <td style="background-color: red"></td>
                @else
                    <td class="btn-dark"></td>
                @endif
            @endforeach
        </tr>
        @endforeach
        <tr>
            <td style="position:absolute; width:3em; left:0;"></td>
            @foreach($days as $day)
                <td>
                    <?php echo CalendarLink::generateLink($season->name, \Carbon\Carbon::parse($day." ".$season->start_hour) , \Carbon\Carbon::parse($day." ".$season->start_hour)->addHour(2)) ?>
                </td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
    <span>1: 1E UUR ENKEL / 2E UUR DUBBEL</span><br>
    <span>2: 2E UUR ENKEL / 1E UUR DUBBEL</span><br>
    <span>3: 2 UUR DUBBEL</span><br>
    <span style="background-color: red">Kan niet spelen</span><br>
    <span class="btn-dark">Beschikbaar voor eventuele vervanging</span><br>
    <span><img src="{{asset('images/calenderIcon.jpg') }}"> Toevoegen aan agenda</span>
    
<br><hr><br>
<div style="overflow-x:auto;margin-left:4em;">
    <table class="table">
        <thead>
        <tr>
            <th scope="col" style="position:absolute; width:3em; left:0;">Naam</th>
            <th scope="col" style="text-align: center;">Tegen</th>
            <th scope="col" style="text-align: center;">Ploeg 1</th>
            <th scope="col" style="text-align: center;">Ploeg 2</th>
            <th scope="col" style="text-align: center;">Ploeg 3</th>
            <th scope="col" style="text-align: center;">Totaal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($seasonUsers as $user)
            <tr>
                <td style="position:absolute; width:3em; left:0;">{{$user->firstname}}</td>
                <td style="text-align: center">{{ isset($seasonArray['stats'][$user->id]['against']) === true ? count($seasonArray['stats'][$user->id]['against']) : 0 }}</td>
                <td style="text-align: center">{{ isset($seasonArray['stats'][$user->id]['team1']) === true ? $seasonArray['stats'][$user->id]['team1'] : 0 }}</td>
                <td style="text-align: center">{{ isset($seasonArray['stats'][$user->id]['team2']) === true ? $seasonArray['stats'][$user->id]['team2'] : 0 }}</td>
                <td style="text-align: center">{{ isset($seasonArray['stats'][$user->id]['team3']) === true ? $seasonArray['stats'][$user->id]['team3'] : 0 }}</td>
                <td style="text-align: center">{{ isset($seasonArray['stats'][$user->id]['total']) === true ? $seasonArray['stats'][$user->id]['total'] : 0 }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>