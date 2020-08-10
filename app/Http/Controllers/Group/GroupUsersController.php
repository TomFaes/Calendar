<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroupUser;
use App\Services\GroupService;
use App\Validators\GroupUserValidation;

class GroupUsersController extends Controller
{
    /** App\Repositories\Contracts\IGroup */
    protected $group;

    /** App\Repositories\Contracts\IGroupUser */
    protected $groupUser;
    /** App\Validators\GroupUserValidation */
    protected $groupUserValidation;

    protected $groupService;

    public function __construct(GroupUserValidation $groupUserValidation, IGroupUser $groupUser) 
    {
        $this->middleware('auth:api');

        $this->middleware('groupuser')->except('show');

        $this->groupUserValidation = $groupUserValidation;
        $this->groupUser = $groupUser;

        //Check if the user is already added to a group
        $this->groupService = resolve(GroupService::class);
    }

    public function index($group)
    {        
        $groupUsers = $this->groupUser->getUsersOfGroup($group);
        return response()->json($groupUsers, 200);
    }

    public function store(Request $request)
    {
        $this->groupUserValidation->validateGroupUser($request);
        $groupUser = $this->groupUser->create($request->all());

        
        //check if the user exist and match the user to the usergroup
        if ($groupUser->email != "") {
            $this->groupService->checkExistingUser($groupUser->email);
        }

        return response()->json($groupUser, 200);
    }

    public function show($id)
    {
        return response()->json($this->groupUser->getGroupUser($id), 200);
    }

    public function update(Request $request, $group, $id)
    {
        $this->groupUserValidation->validateGroupUser($request);
        $groupUser = $this->groupUser->update($request->all(), $id);

        //check if the user exist and match the user to the usergroup
        if ($groupUser->email != "") {
            $this->groupService->checkExistingUser($groupUser->email);
        }
        return response()->json($groupUser, 201);
    }

    public function destroy($group, $id)
    {
        $this->groupUser->delete($id);
        return response()->json("Group user is deleted", 204);
    }
}
