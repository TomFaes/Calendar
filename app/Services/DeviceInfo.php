<?php

namespace App\Services;

class DeviceInfo 
{
    
    public static function getOs()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        // Detect Device/Operating System
        if (preg_match('/android/i',$agent)) $os = 'Android';
        elseif (preg_match('/Linux/i',$agent)) $os = 'Linux';
        elseif (preg_match('/Mac/i',$agent)) $os = 'Mac'; 
        elseif (preg_match('/iPhone/i',$agent)) $os = 'iPhone'; 
        elseif (preg_match('/iPad/i',$agent)) $os = 'iPad'; 
        elseif (preg_match('/Droid/i',$agent)) $os = 'Droid'; 
        elseif (preg_match('/Unix/i',$agent)) $os = 'Unix'; 
        elseif (preg_match('/Windows/i',$agent)) $os = 'Windows';
        else $os = 'Unknown';
        
        return $os;
    }
    
    
    
}
