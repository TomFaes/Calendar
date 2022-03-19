<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeasonCollection;
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
    
    public function __construct(ISeason $seasonRepo)
    {
        //$this->middleware('auth:api');
        $this->season = $seasonRepo;
    }
    
    /**
     * Get all active calendars
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(new SeasonCollection($this->season->getActiveSeasons(Auth::user()->id)), 200);
    }
}
