<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupUserRequest;
use App\Http\Resources\GroupUserCollection;
use App\Http\Resources\GroupUserResource;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;

use App\Services\GroupUserService;

class GroupUsersController extends Controller
{
    protected $groupUserService;

    public function __construct(GroupUserService $groupUserService) 
    {
        $this->groupUserService = $groupUserService;
    }

    public function index(Group $group)
    {        
        $groupUsers = GroupUser::with(['group', 'user'])
                        ->where('group_id', $group->id)
                        ->OrderBy('firstname', 'asc', 'name', 'asc')
                        ->get();
        return response()->json(new GroupUserCollection($groupUsers), 200);
    }

    public function store(GroupUserRequest $request, Group $group)
    {
        $validated = $request->validated();
        $validated['group_id'] = $group->id;
        $validated['code'] = $this->groupUserService->createCode();
        $groupUser = GroupUser::create($validated);
        return response()->json(new GroupUserResource($groupUser), 200);
    }

    public function update(GroupUserRequest $request, Group $group, GroupUser $groupUser)
    {
        $groupUser->update($request->validated());
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function destroy(Group $group, GroupUser $groupUser)
    {
        $groupUser->delete();
        return response()->json("Group user is deleted", 204);
    }

    public function joinGroup(Request $request)
    {
        $userId = auth()->user()->id;

        $groupUser = $this->groupUserService->joinGroup($request->code, $userId);
        if($groupUser == false){
            return response()->json("There is no match for this code", 204);
        }
        return response()->json(new GroupUserResource($groupUser), 201);
    }

    public function regenerateGroupUserCode(Group $group, GroupUser $groupUser)
    {
        $groupUser = $this->groupUserService->regenerateGroupUserCode($groupUser);
        return response()->json(new GroupUserResource($groupUser), 201);
    }
}
