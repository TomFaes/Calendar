@component('mail::message')

Beste, 

Op {{ $calendar['date'] }} is het weeral zover, onze wekelijkse tennisdag. Het schema voor deze week ziet er als volgende uit.

<ul>
    <li>Ploeg 1: <b>{{ $calendar['player1']['player'] ?? "" }}</b> & <b>{{ $calendar['player2']['player'] ?? "" }}</b></li>
    <li>Ploeg 2: <b>{{ $calendar['player3']['player'] ?? "" }}</b> & <b>{{ $calendar['player4']['player'] ?? "" }}</b></li>
    @if ($season->type == "TwoFieldTwoHourThreeTeams" || $season->type == "TwoFieldTwoHourFourTeams")
        <li>Ploeg 3: <b>{{ $calendar['player5']['player'] ?? "" }}</b> & <b>{{ $calendar['player6']['player'] ?? "" }}</b></li>
    @endif
    @if ($season->type == "TwoFieldTwoHourFourTeams")
        <li>Ploeg 4: <b>{{ $calendar['player7']['player'] ?? "" }}</b> & <b>{{ $calendar['player8']['player'] ?? "" }}</b></li>
    @endif
</ul>

@if ($season->type == "TwoFieldTwoHourThreeTeams")
<div style="padding: 10px 0px 10px 0px">
    Ploeg 1: eerste uur enkel, 2de uur dubbel<br>
    Ploeg 2: eerste uur dubbel, 2de uur enkel<br>
    Ploeg 3: 2 uur dubbel
</div>
@endif

<!-- Alleen maar gebruiken als dit in productie komt
@component('mail::button', ['url' => $url])
Ga naar website
@endcomponent
-->

Met vriendelijke groeten,<br>
{{ $season->admin->fullName }}
@endcomponent

