<?php

namespace App\Http\Controllers\Group;
use App\Http\Controllers\Controller;

use App\Group;
use App\Http\Resources\GroupCollection;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroup;

use Auth;

class UserGroupsController extends Controller
{
    /** App\Repositories\Contracts\IGroup */
    protected $group;

    public function __construct(IGroup $group) 
    {
        $this->middleware('auth:api');

        $this->group = $group;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = auth()->user()->id;
        return response()->json(new GroupCollection($this->group->getUserGroups($userId)), 200);
    }
}
