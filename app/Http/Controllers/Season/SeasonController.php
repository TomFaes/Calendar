<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeasonRequest;
use App\Http\Resources\SeasonCollection;
use App\Http\Resources\SeasonResource;
use App\Models\Absence;
use App\Models\Season;
use Illuminate\Support\Facades\Auth;

use App\Services\SeasonService;

class SeasonController extends Controller
{
    protected $seasonService;
    
    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }
    
    public function index()
    {
        $userId = Auth::user()->id;
        $seasonsOfUser = 
            Season::whereHas('teams.group_user', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })
            ->orwhereHas('group.groupUsers', function ($query) use ($userId) {
                $query->where('user_id', '=', $userId);
            })
            ->orWhere('admin_id', $userId)
            //->with(['group', 'admin', 'group.groupUsers'])
            ->get();
            return response()->json(new SeasonCollection($seasonsOfUser), 200);
    }
    
    public function store(SeasonRequest $request)
    {
        $validated = $request->validated();
        $validated['admin_id'] = auth()->user()->id;
        $validated['day'] = $this->seasonService->getDutchDay($validated['begin']);
        $season = Season::create($validated);
        return response()->json(new SeasonResource($season), 200);
    }

    public function show(Season $season)
    {
        return response()->json(new SeasonResource($season), 200);
    }
    
    public function update(SeasonRequest $request, Season $season)
    {
        $userId = Auth::user()->id;
        $validated = $request->validated();
        $season->update($validated);
        $season->admin_id = $request['admin_id'] ?? $userId;
        $season->day = $this->seasonService->getDutchDay($validated['begin']);
        $season->save();
        return response()->json(new SeasonResource($season), 200);
    }

    public function seasonIsGenerated(Season $season){
        $season->is_generated = 1;
        $season->save();
        return response()->json(new SeasonResource($season), 200);
    }

    public function destroy(Season $season)
    {
        if($season->is_generated != 0){
            return response()->json($season->name." is a generated season and cannot be deleted", 200);
        }
        Absence::where('season_id', $season->id)->delete();
        $season->delete();
        return response()->json("Season is deleted", 202);
    }
}
