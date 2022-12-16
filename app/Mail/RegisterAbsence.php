<?php

namespace App\Mail;

use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterAbsence extends Mailable
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
        $url = env('APP_URL')."season/".$this->season->id."/absence";

        return $this->markdown('emails.seasons.absence')
                    ->subject('Geef je afwezigheden op voor het nieuwe seizoen: ' . $this->season->name)
                    ->with('season', $this->season)
                    ->with('url', $url);
    }
}
