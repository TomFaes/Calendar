<?php
namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validators\GroupValidation;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IGroupUser;

class GroupController extends Controller
{
    /** @var App\Validators\GroupValidation */
    protected $groupValidation;
    
    /** @var App\Repositories\Contracts\IGroup */
    protected $group;
    
    /** @var App\Repositories\Contracts\IUser */
    protected $user;

    /** repo App\Repositories\Contracts\IGroupUser */
    protected $groupUser;
    
    public function __construct(GroupValidation $groupValidation, IGroup $group, IUser $user, IGroupUser $groupUser)
    {
        $this->middleware('auth:api');
        $this->middleware('group')->except('store');
        
        $this->groupValidation = $groupValidation;
        $this->group = $group;
        $this->user = $user;
        
        $this->groupUser = $groupUser;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->group->getGroups(20), 200);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //when creating the admin of the group is always the logged in user
        $userId = auth()->user()->id;
        $this->groupValidation->validateCreateGroup($request);
        $group = $this->group->create($request->all(), $userId);

        //add when the group users are created
        $data['firstname'] = auth()->user()->firstname;
        $data['name'] = auth()->user()->name;
        $data['email'] = auth()->user()->email;
        $data['group_id'] = $group->id;
        $data['user_id'] = auth()->user()->id;
        $groupUser = $this->groupUser->create($data);
        $groupUser = $this->groupUser->verifyUser($groupUser->id);
        
        return response()->json($group, 200);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->groupValidation->validateCreateGroup($request);
        $group = $this->group->update($request->all(), $id);
        return response()->json($group, 201);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->group->delete($id);
        return response()->json("Group is deleted", 204);
    }
}
