<?php

namespace App\Http\Controllers\Group;
use App\Http\Controllers\Controller;

use App\Group;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroup;
use App\Validators\GroupValidation;

use Auth;

class UserGroupsController extends Controller
{
    /** App\Repositories\Contracts\IGroup */
    protected $group;
    /** App\Validators\GroupValidation */
    protected $groupValidation;


    public function __construct(GroupValidation $groupValidation, IGroup $group) 
    {
        $this->middleware('auth:api');

        $this->groupValidation = $groupValidation;
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
        return response()->json($this->group->getUserGroups($userId), 200);
    }
}
