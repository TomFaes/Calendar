<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'season_id','player_id', 'date',
    ];

    public function player_one(){
        return $this->belongsTo('App\Models\User', 'player_id', 'id');
    }

    public function season(){
        return $this->belongsTo('App\Models\Season', 'season_id', 'id');
    }    
}
