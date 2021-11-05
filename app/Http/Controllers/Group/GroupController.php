<?php
namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IGroupUser;

class GroupController extends Controller
{    
    /** @var App\Repositories\Contracts\IGroup */
    protected $group;
    
    /** @var App\Repositories\Contracts\IUser */
    protected $user;

    /** repo App\Repositories\Contracts\IGroupUser */
    protected $groupUser;
    
    public function __construct(IGroup $group, IUser $user, IGroupUser $groupUser)
    {
        $this->middleware('auth:api');
        $this->middleware('group')->except('store');

        $this->group = $group;
        $this->user = $user;
        
        $this->groupUser = $groupUser;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function index()
    {
        return response()->json($this->group->getGroups(20), 200);
    }
    */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        //when creating the admin of the group is always the logged in user
        $userId = auth()->user()->id;
        $group = $this->group->create($request->all(), $userId);

        //add when the group users are created
        $data['firstname'] = auth()->user()->firstname;
        $data['name'] = auth()->user()->name;
        $data['email'] = auth()->user()->email;
        $data['group_id'] = $group->id;
        $data['user_id'] = auth()->user()->id;
        $groupUser = $this->groupUser->create($data);

        $this->groupUser->joinGroup($groupUser->code, $userId);
        
        return response()->json(new GroupResource($group), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = $this->group->getGroup($id);
        return response()->json(new GroupResource($group), 200);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, $id)
    {
        $group = $this->group->update($request->all(), $id);
        return response()->json(new GroupResource($group), 201);
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
        return response()->json("Group is deleted", 202);
    }
}
