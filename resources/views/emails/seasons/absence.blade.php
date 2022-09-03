@component('mail::message')
# {{ $season->name }}

Het nieuwe seizoen komt er aan, tijd om je afwezigheden in te geven zodat we een nieuw seizoen vol spannende donderdagen kunnen genereren. 

@component('mail::button', ['url' => $url])
Ingeven afwezigheden
@endcomponent


Thanks,<br>
{{ $season->admin->fullName }}
@endcomponent
