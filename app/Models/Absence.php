<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'season_id','user_id', 'date',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'group_user_id', 'id')->with('user');
    }

    public function season()
    {
        return $this->belongsTo('App\Models\Season', 'season_id', 'id');
    }
}
