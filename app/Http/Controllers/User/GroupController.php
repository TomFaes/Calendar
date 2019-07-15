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
        $this->middleware('group:', ['only' => ['edit', 'update']]);
        $this->middleware('admin:Editor', ['only' => ['index', 'create', 'store']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('group.index')->with('groups', $this->group->getAllGroups());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('group.create')->with('users', $this->user->getAllUsers());
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->groupValidation->validateCreateGroup($request);
        $this->group->create($request);
        return redirect()->to('group/')->send();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $group = $this->group->getGroup($id);
        $users = $this->user->getAllUsers();
        return view('group.edit')->with('group', $group)->with('users', $users);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->groupValidation->validateCreateGroup($request);
        $this->group->update($request, $id);
        return redirect()->to('group/')->send();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->group->delete($id);
        return redirect()->to('group/')->send();
    }
}
