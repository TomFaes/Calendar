<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeasonCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;

class ActiveSeasonController extends Controller
{
   protected $seasonRepo;

    public function __construct(ISeason $seasonRepo)
    {
        $this->seasonRepo = $seasonRepo;
    }
    
    public function index()
    {
        return response()->json(new SeasonCollection($this->seasonRepo->getActiveSeasons(Auth::user()->id)), 200);
    }
}
