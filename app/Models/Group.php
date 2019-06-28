<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name','admin_id',
    ];
    
    public function admin(){
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }
    
    public function users(){
        return $this->belongsToMany('App\Models\User', 'users_groups');
    }

    public function seasonGroup(){
        return $this->hasMany('App\Models\Season', 'group_id', 'id');
    }
}
