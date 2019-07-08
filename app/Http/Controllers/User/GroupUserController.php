<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Validators\GroupValidation;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\IUser;

class GroupUserController extends Controller
{    
    /** @var App\Validators\GroupValidation */
    protected $groupValidation;
    /** @var App\Repositories\Contracts\IGroup */
     protected $group;
     
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    public function __construct(IGroup $group, IUser $user){
        $this->middleware('auth');
        $this->group = $group;
        $this->user = $user;
        
        
        $this->middleware('group:', ['only' => ['edit', 'update']]);
        $this->middleware('admin:Editor', ['only' => ['show', 'create', 'store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $groupUsers = $this->group->getArrayOfGroupUsers($id);
        $nonGroupUsers = array();
        foreach($this->user->getAllUsers() as $user){
            if(isset($groupUsers[$user->id]) === false){
                $nonGroupUsers[$user->id] = $user;
            }
        }
        $group = $this->group->getGroup($id);
        $users = $this->user->getAllUsers();
        
        return view('group.groupUsers')->with('group', $group)->with('users', $users)->with('nonGroupUsers', $nonGroupUsers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $groupId)
    {
        //echo $groupId;
        //dd($request);


        //$this->groupValidation->validateAddUser($request);
        $this->group->addUsers($request, $groupId);
        
        return redirect()->to('group/'.$groupId.'/user/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $groupId)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $userId)
    {
        $this->group->deleteGroupUser($userId, $id);
        return redirect()->to('group/'.$id.'/user/');
    }
}
