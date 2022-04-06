<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeasonRequest;
use App\Http\Resources\SeasonCollection;
use App\Http\Resources\SeasonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;

class SeasonController extends Controller
{
    protected $seasonRepo;
    protected $absenceRepo;
    
    public function __construct(ISeason $seasonRepo, IAbsence $absenceRepo)
    {
        $this->middleware('season')->except('store', 'index');
        $this->seasonRepo = $seasonRepo;
        $this->absenceRepo = $absenceRepo;
    }
    
    public function index()
    {
        return response()->json(new SeasonCollection($this->seasonRepo->getSeasonsOfUser(Auth::user()->id)), 200);
    }
    
    public function store(SeasonRequest $request)
    {
        $userId = auth()->user()->id;
        $season = $this->seasonRepo->create($request->all(), $userId);
        return response()->json(new SeasonResource($season), 200);
    }

    public function show($id)
    {
        return response()->json(new SeasonResource($this->seasonRepo->getSeason($id)), 200);
    }
    
    public function update(SeasonRequest $request, $id)
    {
        $season = $this->seasonRepo->update($request->all(), $id);
        return response()->json(new SeasonResource($season), 200);
    }

    public function seasonIsGenerated($seasonId){
        $season = $this->seasonRepo->seasonIsGenerated($seasonId);
        return response()->json(new SeasonResource($season), 200);
    }

    public function destroy($id)
    {
        $season = $this->seasonRepo->getSeason($id);
        if($season->is_generated != 0){
            return response()->json($season->name." is a generated season and cannot be deleted", 200);
        }
        $this->absenceRepo->deleteSeasonAbsence($id);
        $this->seasonRepo->delete($id);
        return response()->json("Season is deleted", 202);
    }
}
