<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Sanctum\HasApiTokens;

//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function groupUsers()
    {
        return $this->belongsToMany('App\Models\Group', 'users_groups');
    }

    public function userTeamsOne()
    {
        return $this->hasMany('App\Models\Team', 'player_id', 'id');
    }
    
    public function groups()
    {
        return $this->hasMany('App\Models\Group', 'admin_id', 'id');
    }

    public function seasonAdmin()
    {
        //return $this->hasMany('App\Models\Season', 'admin_id', 'id');
    }

    public function absence()
    {
        return $this->belongsTo('App\Models\Absence', 'id', 'user_id');
    }

    protected $appends = ['fullName'];

    public function getFullNameAttribute()
    {
        return $this->firstname." ".$this->name;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


}
