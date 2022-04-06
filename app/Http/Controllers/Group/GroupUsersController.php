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
    protected $groupUserRepo;

    public function __construct(IGroupUser $groupUser) 
    {
        $this->middleware('groupuser')->except('joinGroup');

        $this->groupUserRepo = $groupUser;
    }

    public function index($group_id)
    {        
        $groupUsers = $this->groupUserRepo->getUsersOfGroup($group_id);
        return response()->json(new GroupUserCollection($groupUsers), 200);
    }

    public function store(GroupUserRequest $request)
    {
        $groupUser = $this->groupUserRepo->create($request->all());
        return response()->json(new GroupUserResource($groupUser), 200);
    }

    public function update(GroupUserRequest $request, $group_id, $id)
    {
        $groupUser = $this->groupUserRepo->update($request->all(), $id);
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function destroy($group_id, $id)
    {
        $this->groupUserRepo->delete($id);
        return response()->json("Group user is deleted", 204);
    }

    public function joinGroup(Request $request)
    {
        $userId = auth()->user()->id;

        $groupUser = $this->groupUserRepo->joinGroup($request->code, $userId);
        if($groupUser == false){
            return response()->json("There is no match for this code", 204);
        }
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function regenerateGroupUserCode($group_id, $id)
    {
        $groupUser = $this->groupUserRepo->regenerateGroupUserCode($id);
        return response()->json(new GroupUserResource($groupUser), 201);
    }
}
