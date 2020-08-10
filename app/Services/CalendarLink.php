<?php

namespace App\Services;

use Spatie\CalendarLinks\Link;

class CalendarLink 
{
    public static function generateLink($title, $beginHour, $endHour)
    {
        $os = DeviceInfo::getOs();
        
        $link = Link::create($title, $beginHour, $endHour);
        if ($os == 'Android') {
            return '<a href="'.$link->google().'"><img src='.asset('images/calenderIcon.jpg').'></a>'; 
        } elseif ($os == 'Iphone' OR $os == 'Windows') {
            return '<a href="'.$link->ics().'"><img src='.asset('images/calenderIcon.jpg').'></a>'; 
        }
        return '<a href="'.$link->ics().'"><img src='.asset('images/calenderIcon.jpg').'></a>'; 
    }
}
