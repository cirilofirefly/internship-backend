<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedIntern extends Model
{
    protected $table = "assigned_interns";

    protected $fillable = [
        'intern_user_id',
        'supervisor_user_id',
    ];

    public function supervisor()
    {
        return $this->belongsTo('App\Models\User', 'supervisor_user_id', 'id');
    }

    public function intern()
    {
        return $this->hasOne('App\Models\User', 'id', 'intern_user_id');
    }

    public function dailyTimeRecords()
    {
        return $this->hasMany('App\Models\DailyTimeRecord', 'user_id', 'intern_user_id');
    }

}
