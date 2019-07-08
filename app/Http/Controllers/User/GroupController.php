<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Validators\GroupValidation;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IUser;

class GroupController extends Controller
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
        //
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
    }
    
    public function destroy($id){
        $this->group->delete($id);
        return redirect()->to('group/')->send();
    }
}
