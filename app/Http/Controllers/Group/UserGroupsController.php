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
    protected $groupRepo;

    public function __construct(IGroup $group) 
    {
        $this->groupRepo = $group;
    }

    public function index()
    {
        $userId = auth()->user()->id;
        return response()->json(new GroupCollection($this->groupRepo->getUserGroups($userId)), 200);
    }
}
