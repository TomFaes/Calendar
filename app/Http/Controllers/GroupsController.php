<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Validators\GroupValidation;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IUser;

class GroupsController extends Controller
{
    /** @var App\Validators\GroupValidation */
    protected $groupValidation;
    
    /** @var App\Repositories\Contracts\IGroup */
    protected $group;
    
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    public function __construct(GroupValidation $groupValidation, IGroup $group, IUser $user){
        $this->middleware('auth');
        $this->groupValidation = $groupValidation;
        $this->group = $group;
        $this->user = $user;
        
        $this->middleware('group:delete', ['only' => ['destroy']]);
        $this->middleware('group:', ['only' => ['edit', 'update', 'groupUsers', 'addUsers', 'deleteGroupUser']]);
        $this->middleware('admin:Editor', ['only' => ['index', 'create', 'store']]);
    }
    
    public function index(){
        return view('group.index')->with('groups', $this->group->getAllGroups());
    }

    public function create(){
        return view('group.create')->with('users', $this->user->getAllUsers());
    }
    
    public function store(Request $request){
        $this->groupValidation->validateCreateGroup($request);
        $this->group->create($request);
        return redirect()->to('group/')->send();
    }
    
    public function show($id){
        
    }
    
    public function edit($id){
        $group = $this->group->getGroup($id);
        $users = $this->user->getAllUsers();
        return view('group.edit')->with('group', $group)->with('users', $users);
    }
    
    public function update(Request $request, $id){
        $this->groupValidation->validateCreateGroup($request);
        $this->group->update($request, $id);
        return redirect()->to('group/')->send();
        //return Redirect::to('group');
    }
    
    public function destroy($id){
        $this->group->delete($id);
        return redirect()->to('group/')->send();
    }
    
    public function groupUsers($groupId){
        $groupUsers = $this->group->getArrayOfGroupUsers($groupId);
        $nonGroupUsers = array();
        foreach($this->user->getAllUsers() as $user){
            if(isset($groupUsers[$user->id]) === false){
                $nonGroupUsers[$user->id] = $user;
            }
        }
        $group = $this->group->getGroup($groupId);
        $users = $this->user->getAllUsers();
        
        return view('group.groupUsers')->with('group', $group)->with('users', $users)->with('nonGroupUsers', $nonGroupUsers);
    }
    
    public function addUsers(Request $request, $groupId){
        $this->groupValidation->validateAddUser($request);
        $this->group->addUsers($request, $groupId);
        
        return redirect()->to('group/group-users/'.$groupId);
    }
    
    public function deleteGroupUser(Request $request, $groupId){
        $this->group->deleteGroupUser($request['userId'], $groupId);
        return redirect()->to('group/group-users/'.$groupId);
    }
    
    
    
}
