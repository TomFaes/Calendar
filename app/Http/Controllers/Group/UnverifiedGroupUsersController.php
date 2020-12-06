<?php

namespace App\Http\Controllers\Group;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Repositories\Contracts\IGroupUser;
use App\Services\GroupService;
use App\Validators\GroupUserValidation;
use Auth;

class UnverifiedGroupUsersController extends Controller
{
     /** App\Repositories\Contracts\IGroupUser */
     protected $groupUser;
     /** App\Validators\GroupUserValidation */
     protected $groupUserValidation;


     public function __construct(GroupUserValidation $groupUserValidation, IGroupUser $groupUser) {
        $this->middleware('auth')->only('view');
        $this->middleware('auth:api')->except('view');

        $this->groupUserValidation = $groupUserValidation;
        $this->groupUser = $groupUser;
     }

     public function index(){
        $userId = Auth::user()->id;
        $groupService = resolve(GroupService::class);
        $groupService->checkExistingUser(Auth::user()->email);

        $groupUsers = $this->groupUser->getUnverifiedGroupUsers($userId);
        return response()->json($groupUsers, 200);
     }

    public function update($id)
    {
        $userId = auth()->user()->id;
        $groupUsers = $this->groupUser->getGroupUser($id);

        if($groupUsers->user_id == $userId){
            $groupUser = $this->groupUser->verifyUser($id);
            return response()->json($groupUser, 200);
        }
        return response()->json("user isn't verified", 200);
    }

    public function destroy($id)
    {
        $userId = auth()->user()->id;
        $groupUsers = $this->groupUser->getGroupUser($id);

        if($groupUsers->user_id == $userId){
            $groupUser = $this->groupUser->unverifyUser($id);
            return response()->json($groupUser, 200);
        }
        return response()->json("user isn't unverified", 200);
    }
}
