<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'name','admin_id', 'groep_id', 'begin', 'end',
    ];

    public function admin(){
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }

    public function group(){
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function absence(){
        return $this->belongsTo('App\Models\Absence', 'id', 'season_id');
    }

    public function teams(){
        return $this->hasMany('App\Models\Team', 'season_id', 'id')->orderBy('date', 'asc')->orderBy('team', 'asc');
    }
}
