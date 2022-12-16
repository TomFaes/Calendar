<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeasonCollection;
use Illuminate\Support\Facades\Auth;

use App\Services\SeasonService;

class ActiveSeasonController extends Controller
{   
    public function __invoke()
    {
        $seasonService = new SeasonService();
        $userId = Auth::user()->id;
        $seasons = $seasonService->getActiveSeasons($userId);
        return response()->json(new SeasonCollection($seasons), 200);
    }
}
