<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Season extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = [
        'name','admin_id', 'group_id', 'begin', 'end', 'public', 'allow_replacement', 'type', 'start_hour', 'day'
    ];

    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function absence()
    {
        return $this->belongsTo('App\Models\Absence', 'id', 'season_id');
    }

    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'season_id', 'id')->orderBy('date', 'asc')->orderBy('team', 'asc');
    }

    protected $appends = ['seasonDraw', 'typeMember'];

    //get the number of teams that is drawn
    public function getSeasonDrawAttribute()
    {
        return  $this->hasMany('App\Models\Team', 'season_id', 'id')->count();
    }

    

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
