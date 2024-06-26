<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    const VALIDATED = 'validated';
    const TOTAL_HOURS = 486; //Change this to 0 for demo

    protected $table = 'daily_time_records';
    protected $fillable = [
        'date',
        'am_start_time',
        'am_end_time',
        'pm_start_time',
        'pm_end_time',
        'overtime_start_time',
        'overtime_end_time',
        'description',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'portal_id', 'id');
    }

    public function intern()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function detailedReport()
    {
        return $this->belongsTo('App\Models\DetailedReport', 'id', 'daily_time_record_id');
    }

    public function proofs()
    {
        return $this->hasMany('App\Models\DTRProof', 'daily_time_record_id', 'id');
    }
}
