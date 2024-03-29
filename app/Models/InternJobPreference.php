<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternJobPreference extends Model
{
    protected $table = "intern_job_preferences";

    protected $fillable = [
        'intern_user_id',
        'evaluator_user_id',
        'evaluation',
        'job_preference'
    ];

    public function supervisor()
    {
        return $this->belongsTo('App\Models\User', 'supervisor_user_id', 'id');
    }

    public function intern()
    {
        return $this->hasOne('App\Models\User', 'id', 'intern_user_id');
    }
    
}
