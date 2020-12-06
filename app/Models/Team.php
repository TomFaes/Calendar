<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'season_id','player_id', 'date', 'ask_for_replacement',
    ];

    public function player_one()
    {
        return $this->belongsTo('App\Models\User', 'player_id', 'id')->select(['id', 'firstname', 'name']);
    }

    public function group_user() 
    {
        return $this->belongsTo('App\Models\GroupUser', 'group_user_id', 'id')->select(['id', 'firstname', 'name', 'user_id']);
    }

    public function season()
    {
        return $this->belongsTo('App\Models\Season', 'season_id', 'id');
    }    
}
