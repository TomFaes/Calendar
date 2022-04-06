<?php
namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;

use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IGroupUser;

class GroupController extends Controller
{    
    protected $groupRepo;
    protected $groupUser;
    
    public function __construct(IGroup $group, IGroupUser $groupUser)
    {
        $this->middleware('group')->except('store');
        $this->groupRepo = $group;
        $this->groupUserRepo = $groupUser;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $adminId = auth()->user()->id;
        $group = $this->groupRepo->create($request->all(), $adminId);

        //add the admin to the group users
        $data['firstname'] = auth()->user()->firstname;
        $data['name'] = auth()->user()->name;
        $data['email'] = auth()->user()->email;
        $data['group_id'] = $group->id;
        $data['user_id'] = auth()->user()->id;
        $groupUser = $this->groupUserRepo->create($data);

        $this->groupUserRepo->joinGroup($groupUser->code, $adminId);
        
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
        $group = $this->groupRepo->getGroup($id);
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
        $group = $this->groupRepo->update($request->all(), $id);
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
        $this->groupRepo->delete($id);
        return response()->json("Group is deleted", 202);
    }
}
