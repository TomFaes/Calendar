<?php
namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Services\GroupUserService;

class GroupController extends Controller
{    
    protected $groupUserService;
    
    public function __construct(GroupUserService $groupUserService)
    {
        $this->groupUserService = $groupUserService;
    }
    
    public function store(GroupRequest $request)
    {
        $adminId = auth()->user()->id;

        $validated = $request->validated();
        $validated['admin_id'] = $adminId;
        $group = Group::create($validated);

        //add the admin to the group users
        $data['firstname'] = auth()->user()->firstname;
        $data['name'] = auth()->user()->name;
        $data['group_id'] = $group->id;
        $data['user_id'] = auth()->user()->id;
        $data['code'] = $this->groupUserService->createCode();
        $groupUser = GroupUser::create($data);

        $this->groupUserService->joinGroup($groupUser->code, $adminId);
        return response()->json(new GroupResource($group), 200);
    }

    public function show(Group $group)
    {
        return response()->json(new GroupResource($group), 200);
    }
    
    public function update(GroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        $group->save();
        return response()->json(new GroupResource($group), 201);
    }
    
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json("Group is deleted", 202);
    }
}
