<?php

namespace App\Console\Commands;

use App\Models\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NextPlayDate;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailNextPlayDayCron extends Command implements ShouldQueue
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailNextPlayDay:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a mail to notify the schedule of the users of the season who have to play the next day.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now();
        
        $tomorrow = $date->addDay()->format('Y-m-d');
        $tomorrowDay = $this->convertDayToDutch($date->format('l'));

        $activeSeasons = Season::whereDate('begin', '<= ', $tomorrow)->whereDate ('end', '>=', $tomorrow)->where('day', $tomorrowDay)->get();

        Log::info(' ');
        Log::info('---------------------------');
        Log::info('Mail next play day');
        Log::info("Aantal actieve seizoenen: ".count($activeSeasons));

        foreach($activeSeasons AS $activeSeason){
            if($activeSeason->seasonDraw == 0){
                continue;
            }
            $mailUsers = $this->getMailAdressUsers($activeSeason);
            
            Mail::to($activeSeason->admin->email)
            ->cc($mailUsers)
            //->bcc($mailUsers)
            ->send(new NextPlayDate($activeSeason));

            Log::info($activeSeason->name." begint op ".$activeSeason->begin." en eindigt op ".$activeSeason->end);
            Log::info("mails: ".implode("; ",$mailUsers));
        }
        Log::info('---------------------------');
    }

    private function convertDayToDutch($day)
    {
        switch ($day) {
            case "Monday" : return "ma";break;
            case "Tuesday" : return "di";break;
            case "Wednesday" : return "wo";break;
            case "Thursday" : return "do";break;
            case "Friday" : return "vr";break;
            case "Saturday" : return "za";break;
            case "Sunday" : return "zo";break;
        }
    }

    private function getMailAdressUsers($season)
    {
        $arrayUserMails = array();
        foreach($season->group->groupUsers AS $groupUser){
            if($groupUser->user->email == ""){
                continue;
            }
            $arrayUserMails[] = $groupUser->user->email;
        }
        return $arrayUserMails;
    }
}
