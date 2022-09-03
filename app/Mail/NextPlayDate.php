<?php

namespace App\Mail;

use App\Models\Season;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class NextPlayDate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $season;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = env('APP_URL')."season/".$this->season->id."/day_calendar";
        $currentCalendar = $this->getCalendar();
        
        return $this->markdown('emails.seasons.nextPlayDate')
                    ->subject('Speelschema ' . $this->season->name . ' op ' . $currentCalendar['date'])
                    ->with('season', $this->season)
                    ->with('calendar', $currentCalendar)
                    ->with('url', $url);
                    
    }

    private function getCalendar(){
        $seasonGenerator = GeneratorFactory::generate($this->season->type);
        $calendar = $seasonGenerator->getSeasonCalendar($this->season);
        $currentday = $calendar['currentPlayDay'];

        $currentCalendar = array();
        $currentCalendar['date'] = Carbon::parse($calendar['data'][$currentday]['day'])->format('d/m/Y');
        
        $groupUsers = $calendar['groupUserData'];

        $x = 0;
        foreach($calendar['data'][$currentday]['teams'] AS $key=>$player){
            $x++;
            $groupUserId = $player['groupUserId'];
            $calendar['data'][$currentday]['teams'][$key]['user'] = $groupUsers[$groupUserId]['fullName'] ?? "";
            $key_name = 'player'.$x;
            $currentCalendar[$key_name]['team'] = $player['team'] ?? "";
            $currentCalendar[$key_name]['player'] = $groupUsers[$groupUserId]['fullName'] ?? "";
        }
        return $currentCalendar;
    }
}
