<?php

namespace App\Http\Controllers\Group;
use App\Http\Controllers\Controller;

use App\Http\Resources\GroupCollection;
use App\Services\GroupService;


class UserGroupsController extends Controller
{
    public function index()
    {
        $groupService = new GroupService();
        $userId = auth()->user()->id;
        $groupUsers = $groupService->getUserGroups($userId);
        return response()->json(new GroupCollection($groupUsers), 200);
    }
}
