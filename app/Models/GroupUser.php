<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'firstname', 'name', 'ignore_user', 'ignore_plays', 'code', 'group_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Return the group
     */
    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id')->select(['id', 'name', 'admin_id'])->withDefault();
    }

     /**
     * Returns the user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->withDefault();
    }

    protected $appends = ['fullName'];

    /**
     * Returns a name for dropdowns
     */
    public function getfullNameAttribute()
    {
        if ($this->user->id > 0) {
            return $this->user->firstname." ".$this->user->name;
        }
        return $this->firstname." ".$this->name;
    }
}
