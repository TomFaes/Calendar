<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupUserRequest;
use App\Http\Resources\GroupUserCollection;
use App\Http\Resources\GroupUserResource;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroupUser;

class GroupUsersController extends Controller
{
    /** App\Repositories\Contracts\IGroup */
    protected $group;

    /** App\Repositories\Contracts\IGroupUser */
    protected $groupUser;

    public function __construct(IGroupUser $groupUser) 
    {
        $this->middleware('auth:api');

        $this->middleware('groupuser')->except('joinGroup');

        $this->groupUser = $groupUser;
    }

    public function index($group_id)
    {        
        $groupUsers = $this->groupUser->getUsersOfGroup($group_id);
        return response()->json(new GroupUserCollection($groupUsers), 200);
    }

    public function store(GroupUserRequest $request)
    {
        $groupUser = $this->groupUser->create($request->all());
        return response()->json(new GroupUserResource($groupUser), 200);
    }
/*
    public function show($id)
    {
        return response()->json($this->groupUser->getGroupUser($id), 200);
    }
*/
    public function update(GroupUserRequest $request, $group_id, $id)
    {
        $groupUser = $this->groupUser->update($request->all(), $id);
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function destroy($group_id, $id)
    {
        $this->groupUser->delete($id);
        return response()->json("Group user is deleted", 204);
    }

    public function joinGroup(Request $request)
    {
        $userId = auth()->user()->id;

        $groupUser = $this->groupUser->joinGroup($request->code, $userId);
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function regenerateGroupUserCode($group_id, $id)
    {
        $groupUser = $this->groupUser->regenerateGroupUserCode($id);
        return response()->json(new GroupUserResource($groupUser), 201);
    }

}
