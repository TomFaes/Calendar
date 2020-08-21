<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;

use App\Services\SeasonGeneratorService\GeneratorFactory;

class ActiveSeasonController extends Controller
{
    /** @var App\Repositories\Contracts\ISeason */
   protected $season;

    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    
    /** @var App\Validators\SeasonValidation */
    protected $seasonValidator;
    
    public function __construct(ISeason $seasonRepo)
    {
        $this->middleware('auth:api');
        $this->season = $seasonRepo;
    }
    
    /**
     * Get all active calendars
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active = $this->season->getActiveSeasons(Auth::user()->id);

        $activeSeasonsArray = array();
        foreach ($active as $season) {
            $seasonGenerator = GeneratorFactory::generate($season->type);
            $activeSeasonsArray['season'][] = $seasonGenerator->getSeasonCalendar($season);
        }
        return response()->json($activeSeasonsArray, 200);
    }
}
