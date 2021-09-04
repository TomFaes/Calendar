<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = [
        'name','admin_id',
    ];
    
    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }
    
    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'group_id', 'id')->with('user');
    }

    public function groupSeasons()
    {
        return $this->hasMany('App\Models\Season', 'group_id', 'id');
    }


    protected $appends = ['typeMember'];

    public function getTypeMemberAttribute()
    {
        if (isset(Auth::user()->id) === true) {
            if ($this->admin_id == Auth::user()->id) {
                return "Admin";
            }
        }
        return "User";
    }
}
